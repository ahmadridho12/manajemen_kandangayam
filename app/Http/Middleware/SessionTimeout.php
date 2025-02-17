<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $timeout = 60 * 60; // Set durasi timeout dalam detik (misalnya 1 jam)

        if (Auth::check()) {
            $lastActivity = Session::get('last_activity');
            $now = Carbon::now()->timestamp;

            Log::info('Last Activity: ' . $lastActivity);
            Log::info('Current Time: ' . $now);

            // Cek apakah waktu terakhir aktivitas melebihi timeout
            if ($lastActivity && ($now - $lastActivity) > $timeout) {
                Log::info('User  session has expired, logging out.');
                Auth::logout();
                Session::forget('last_activity');

                return redirect()->route('login')->with('message', 'Sesi Anda telah habis. Silakan login kembali.');
            }

            // Update waktu terakhir aktivitas
            Session::put('last_activity', $now);
        }

        return $next($request);
    }
}