<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** Mark one notification read, then send the user on to wherever it points. */
    public function read(AppNotification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->markRead();

        return $notification->url ? redirect($notification->url) : back();
    }

    public function readAll(Request $request)
    {
        Auth::user()->appNotifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back();
    }
}
