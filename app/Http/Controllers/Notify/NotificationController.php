<?php

namespace App\Http\Controllers\Notify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    public function markAsReadAll(Request $request){
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);       
    }

    public function markAsRead(Request $request, $notificationId)
    {
        $notification = $request->user()->unreadNotifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);         
        }

        return response()->json(['error' => true]);
    }
}
