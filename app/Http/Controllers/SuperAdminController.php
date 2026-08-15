<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\SuperAdminNomination;
use App\Models\SuperAdminApproval;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    /** Any logged-in user can apply to become an admin (alongside their existing role) -- a Super Admin decides. */
    public function applyForAdmin(Request $request)
    {
        $user = Auth::user();

        if ($user->is_admin) {
            return back()->with('status', "You're already an admin.");
        }
        if ($user->admin_application_status === 'pending') {
            return back()->with('status', 'Your admin application is already pending review.');
        }

        $user->update(['admin_application_status' => 'pending', 'admin_applied_at' => now()]);

        return back()->with('status', 'Your application to become an admin has been submitted. A Super Admin will review it.');
    }

    /** Super Admin dashboard: pending admin applications + super-admin nominations + the admin/super-admin directory. */
    public function dashboard()
    {
        $pendingApplications = User::where('admin_application_status', 'pending')->get();
        $nominations = SuperAdminNomination::with(['nominee', 'createdBy', 'approvals.approver'])->latest()->get();
        $founders = User::whereIn('email', config('super_admins.emails', []))->get();
        $admins = User::where('is_admin', true)->orWhere('is_super_admin', true)->get();
        $candidates = User::where('is_admin', true)->where('is_super_admin', false)->get(); // eligible nomination pool

        return view('super-admin.dashboard', compact('pendingApplications', 'nominations', 'founders', 'admins', 'candidates'));
    }

    public function approveAdminApplication(Request $request, User $user, BrevoMailService $brevo)
    {
        $user->update(['is_admin' => true, 'admin_application_status' => 'approved']);
        AdminLog::record('approve_admin_application', "Approved {$user->name}'s admin application.", $user->id);

        $brevo->notifyUser(
            $user,
            "You're now an admin — Smart Agri-Advisory Platform",
            'Your admin application was approved 🎉',
            "<p>A Super Admin approved your application. You can now open the Admin Dashboard from the navbar, alongside your usual account.</p>",
            'Open Admin Dashboard',
            route('admin.dashboard')
        );

        return back()->with('status', "{$user->name} is now an admin.");
    }

    public function rejectAdminApplication(Request $request, User $user, BrevoMailService $brevo)
    {
        $user->update(['admin_application_status' => 'rejected']);
        AdminLog::record('reject_admin_application', "Rejected {$user->name}'s admin application.", $user->id);

        $brevo->notifyUser(
            $user,
            'Your admin application — Smart Agri-Advisory Platform',
            'Your admin application was not approved',
            "<p>A Super Admin reviewed your application and did not approve it this time. You're welcome to apply again later.</p>",
        );

        return back()->with('status', "{$user->name}'s application was rejected.");
    }

    /**
     * Create a nomination for a new Super Admin. Anyone who is already an
     * admin/super admin can do this -- but per the rules, if the creator
     * isn't one of the 3 founding emails, this is just a *suggestion*: it
     * still needs all 3 founders' approval like any other nomination, the
     * creator's own approval (if they're not a founder) doesn't count.
     */
    public function createNomination(Request $request, BrevoMailService $brevo)
    {
        abort_unless(Auth::user()->is_admin || Auth::user()->is_super_admin, 403, 'Only admins can suggest a Super Admin.');

        $data = $request->validate(['nominee_user_id' => ['required', 'exists:users,id']]);
        $nominee = User::findOrFail($data['nominee_user_id']);

        if ($nominee->is_super_admin) {
            return back()->with('status', "{$nominee->name} is already a Super Admin.");
        }

        $nomination = SuperAdminNomination::create([
            'nominee_user_id' => $nominee->id,
            'created_by_user_id' => Auth::id(),
        ]);

        // If a founding member creates the nomination, that IS their approval.
        if (Auth::user()->isFoundingSuperAdmin()) {
            $this->recordApproval($nomination, Auth::user(), 'approve', $brevo);
        }

        return back()->with('status', Auth::user()->isFoundingSuperAdmin()
            ? "Nomination created for {$nominee->name} — your approval is recorded. Waiting on the other 2 founders."
            : "Suggested {$nominee->name} as a Super Admin. The 3 founding Super Admins will review it.");
    }

    public function approveNomination(Request $request, SuperAdminNomination $nomination, BrevoMailService $brevo)
    {
        abort_unless(Auth::user()->isFoundingSuperAdmin(), 403, 'Only the 3 founding Super Admins can approve a nomination.');
        $this->recordApproval($nomination, Auth::user(), 'approve', $brevo);

        return back()->with('status', 'Your approval is recorded.');
    }

    public function rejectNomination(Request $request, SuperAdminNomination $nomination, BrevoMailService $brevo)
    {
        abort_unless(Auth::user()->isFoundingSuperAdmin(), 403, 'Only the 3 founding Super Admins can decide a nomination.');
        $this->recordApproval($nomination, Auth::user(), 'reject', $brevo);

        return back()->with('status', 'Nomination rejected.');
    }

    protected function recordApproval(SuperAdminNomination $nomination, User $approver, string $decision, BrevoMailService $brevo): void
    {
        if ($nomination->status !== 'pending') {
            return;
        }

        SuperAdminApproval::updateOrCreate(
            ['nomination_id' => $nomination->id, 'approver_user_id' => $approver->id],
            ['decision' => $decision]
        );

        AdminLog::record('super_admin_' . $decision, "{$approver->name} {$decision}d the Super Admin nomination for {$nomination->nominee->name}.", $nomination->nominee_user_id);

        if ($decision === 'reject') {
            $nomination->update(['status' => 'rejected', 'decided_at' => now()]);
            return;
        }

        $approvals = $nomination->approvals()->where('decision', 'approve')->pluck('approver_user_id');
        $founderApprovals = User::whereIn('id', $approvals)->get()->filter->isFoundingSuperAdmin();

        if ($founderApprovals->count() >= 3) {
            $nomination->update(['status' => 'approved', 'decided_at' => now()]);
            $nominee = $nomination->nominee;
            $nominee->update(['is_super_admin' => true, 'is_admin' => true]);

            $brevo->notifyUser(
                $nominee,
                "You're now a Super Admin — Smart Agri-Advisory Platform",
                'Congratulations — you are now a Super Admin 🎉',
                '<p>All 3 founding Super Admins approved your nomination. You now have full admin access alongside your existing account.</p>',
                'Open Admin Dashboard',
                route('admin.dashboard')
            );
        }
    }

    /** Directory of every admin/super-admin, visible to any admin (per the request: "admin ra dekhte parbe k k admin/super admin"). Also lets them email one directly via mailto. */
    public function directory()
    {
        abort_unless(Auth::user()->is_admin || Auth::user()->is_super_admin, 403, 'Admins only.');

        $admins = User::where('is_admin', true)->orWhere('is_super_admin', true)->orderByDesc('is_super_admin')->orderBy('name')->get();

        return view('super-admin.directory', compact('admins'));
    }
}
