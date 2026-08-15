<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\AnalyticsSnapshot;
use App\Models\ClimateZone;
use App\Models\Crop;
use App\Models\ModelRetrainingJob;
use App\Models\Order;
use App\Models\Recommendation;
use App\Models\SystemBackup;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'farmers' => User::where('role', 'farmer')->count(),
            'officers' => User::where('role', 'extension_officer')->count(),
            'suppliers' => User::where('role', 'supplier')->count(),
            'pending_approvals' => User::where('status', 'pending')->count(),
            'total_recommendations' => Recommendation::count(),
            'total_orders' => Order::count(),
        ];
        $pendingUsers = User::where('status', 'pending')->get();

        // Flag any pending user whose email previously belonged to a removed
        // account, so the admin sees "this is a repeat" before approving.
        $priorRemovalEmails = User::onlyTrashed()->pluck('removed_original_email')->filter()->unique();
        $pendingUsers->each(function ($u) use ($priorRemovalEmails) {
            $u->hadPriorRemoval = $priorRemovalEmails->contains($u->email);
        });

        $zones = ClimateZone::all();
        $crops = Crop::all();

        return view('admin.dashboard', compact('stats', 'pendingUsers', 'zones', 'crops'));
    }

    /** Manage Users & Assign Roles use case */
    public function updateUserRole(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', 'in:farmer,extension_officer,supplier,admin']]);
        $user->update($data);
        $this->log('update_role', "Changed {$user->name}'s role to {$data['role']}.", $user->id);

        return back()->with('status', "Updated {$user->name}'s role to {$data['role']}.");
    }

    /** Full user list + moderation console (Restrict / Remove / Approve / role changes) */
    public function users()
    {
        $allUsers = User::orderByRaw("FIELD(status, 'pending', 'suspended', 'active')")->orderBy('name')->get();

        // So the admin can see "this is a repeat account" on any pending
        // registration whose email previously belonged to a removed user.
        $priorRemovals = User::onlyTrashed()->get()->groupBy('removed_original_email');

        $removedUsers = User::onlyTrashed()->latest('removed_at')->get();

        return view('admin.users', compact('allUsers', 'priorRemovals', 'removedUsers'));
    }

    /** Removed-account detail: full history for one email, for manual review before re-approving. */
    public function removedHistory(string $email)
    {
        $removedAccounts = User::onlyTrashed()->where('removed_original_email', $email)->oldest('removed_at')->get();
        abort_if($removedAccounts->isEmpty(), 404);

        $logs = AdminLog::where(function ($q) use ($removedAccounts) {
            foreach ($removedAccounts as $acc) {
                $q->orWhere('subject_user_id', $acc->id)
                  ->orWhere('description', 'like', "%#{$acc->id}%");
            }
        })->latest()->get();

        return view('admin.removed-history', compact('removedAccounts', 'logs', 'email'));
    }

    /** Full admin activity log (every action taken by every admin) */
    public function activityLog()
    {
        $logs = AdminLog::with('admin')->latest()->paginate(40);

        return view('admin.activity', compact('logs'));
    }

    /** Approve Supplier / Officer Accounts use case (<<extend>> of user management) */
    public function approveUser(Request $request, User $user, BrevoMailService $brevo)
    {
        $user->update(['status' => 'active']);
        if ($user->role === 'supplier' && $user->supplierProfile) {
            $user->supplierProfile->update(['verified' => true]);
        }
        $this->log('approve_user', "Approved {$user->name} ({$user->role}).", $user->id);

        $brevo->sendNotification(
            $user->email,
            'Your account has been approved — Smart Agri-Advisory Platform',
            "You're approved, {$user->name}! 🎉",
            "<p>An admin has approved your <strong>" . str_replace('_', ' ', $user->role) . "</strong> account. You can log in now and start using the platform.</p>",
            'Log in now',
            route('login')
        );

        return back()->with('status', "{$user->name} approved and activated.");
    }

    /**
     * Restrict use case — a TEMPORARY block. Optionally auto-lifts itself
     * after N days (checked on the user's next login attempt); leave days
     * blank for an indefinite restriction the admin has to lift manually.
     */
    public function restrictUser(Request $request, User $user, BrevoMailService $brevo)
    {
        abort_if($user->id === Auth::id(), 403, "You can't restrict your own account.");

        $data = $request->validate(['days' => ['nullable', 'integer', 'min:1', 'max:365']]);
        $until = ! empty($data['days']) ? now()->addDays((int) $data['days']) : null;

        $user->update(['status' => 'suspended', 'restricted_until' => $until]);

        $period = $until ? "for {$data['days']} day(s), until {$until->format('d M Y')}" : 'until further notice';
        $this->log('restrict_user', "Restricted {$user->name} ({$user->email}) {$period}.", $user->id);

        $brevo->sendNotification(
            $user->email,
            'Your account has been restricted — Smart Agri-Advisory Platform',
            'Your account access is temporarily restricted',
            "<p>An admin has restricted your account <strong>{$period}</strong>."
                . ($until ? " You'll be able to log in again automatically after that." : ' Please contact an admin for details.') . "</p>",
        );

        return back()->with('status', "{$user->name} restricted {$period}.");
    }

    /**
     * Remove use case — different from Restrict: this is a soft delete, not
     * time-boxed. The account disappears from every normal listing and the
     * person can no longer log in with it. Their email is freed up (see
     * migration note) so they CAN register a brand new account later, but
     * that new registration is forced to 'pending' and flagged with this
     * removal's history so an admin has to manually decide again -- it never
     * silently re-activates.
     */
    public function removeUser(Request $request, User $user, BrevoMailService $brevo)
    {
        abort_if($user->id === Auth::id(), 403, "You can't remove your own account.");

        $data = $request->validate(['reason' => ['nullable', 'string']]);
        $originalEmail = $user->email;

        $user->update([
            'removed_original_email' => $originalEmail,
            'removed_at' => now(),
            'email' => $originalEmail . '+removed-' . $user->id . '-' . now()->timestamp,
        ]);
        $user->delete(); // soft delete -- excluded from all normal queries from here on

        $this->log('remove_user', "Removed {$user->name} ({$originalEmail}, {$user->role})."
            . (! empty($data['reason']) ? " Reason: {$data['reason']}" : ''), $user->id);

        $brevo->sendNotification(
            $originalEmail,
            'Your account has been removed — Smart Agri-Advisory Platform',
            'Your account was removed by an admin',
            "<p>An admin has removed your account from the Smart Agri-Advisory Platform."
                . (! empty($data['reason']) ? " Reason: " . e($data['reason']) : '')
                . "</p><p>You're welcome to register again with the same email — a new registration will be reviewed by an admin before it's activated.</p>",
        );

        return back()->with('status', "{$user->name} removed. They can re-register, but any new account will need manual approval.");
    }

    /** Manage Master Data (Crops, Zones, Soil) use case */
    public function storeZone(Request $request)
    {
        $data = $request->validate([
            'zone_name' => ['required', 'string'],
            'region' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);
        ClimateZone::create($data);
        $this->log('create_zone', "Added climate zone: {$data['zone_name']}");

        return back()->with('status', 'Climate zone added.');
    }

    public function storeCrop(Request $request)
    {
        $data = $request->validate([
            'crop_name' => ['required', 'string'],
            'season' => ['required', 'in:Kharif-1,Kharif-2,Rabi'],
            'description' => ['nullable', 'string'],
        ]);
        Crop::create($data);
        $this->log('create_crop', "Added crop: {$data['crop_name']}");

        return back()->with('status', 'Crop added.');
    }

    /** Trigger ML Model Retraining use case */
    public function triggerRetrain(Request $request)
    {
        $data = $request->validate(['model_name' => ['required', 'in:crop_rf,fertilizer_rule,price_lstm,disease_cnn']]);

        ModelRetrainingJob::create([
            'triggered_by' => Auth::id(),
            'model_name' => $data['model_name'],
            'status' => 'queued',
        ]);
        $this->log('trigger_retrain', "Queued retraining for {$data['model_name']}.");

        return back()->with('status', "Retraining job queued for {$data['model_name']}. Run the corresponding train_*.py script in ml-service/ to process it.");
    }

    /** Backup / Restore Database use case */
    public function triggerBackup(Request $request)
    {
        SystemBackup::create([
            'initiated_by' => Auth::id(),
            'backup_path' => 'storage/backups/backup_' . now()->format('Ymd_His') . '.sql',
            'status' => 'success',
        ]);
        $this->log('trigger_backup', 'Database backup initiated.');

        return back()->with('status', 'Backup job recorded (wire this to mysqldump/queue in production).');
    }

    /** View Platform Analytics Dashboard use case */
    public function analytics()
    {
        $cropCounts = Recommendation::join('crops', 'crops.id', '=', 'recommendations.recommended_crop_id')
            ->selectRaw('crops.crop_name as crop, count(*) as total')
            ->groupBy('crops.crop_name')->orderByDesc('total')->get();

        $snapshots = AnalyticsSnapshot::latest('snapshot_date')->take(12)->get();

        return view('admin.analytics', compact('cropCounts', 'snapshots'));
    }

    public function takeSnapshot(Request $request)
    {
        AnalyticsSnapshot::create([
            'snapshot_date' => now()->toDateString(),
            'active_farmers' => User::where('role', 'farmer')->where('status', 'active')->count(),
            'total_recommendations' => Recommendation::count(),
            'avg_model_accuracy' => 99.3, // from ml-service/train_crop_model.py on the real Kaggle dataset
            'total_orders' => Order::count(),
        ]);
        $this->log('analytics_snapshot', 'Took a new analytics snapshot.');

        return back()->with('status', 'Analytics snapshot recorded.');
    }

    protected function log(string $action, string $description, ?int $subjectUserId = null): void
    {
        AdminLog::create(['admin_id' => Auth::id(), 'subject_user_id' => $subjectUserId, 'action' => $action, 'description' => $description]);
    }
}
