<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Activation;
use App\Models\Role;
use App\Models\User;
use App\Traits\ActivationTrait;
use App\Traits\CaptureIpTrait;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ActivateController extends Controller
{
    use ActivationTrait;

    private static $userHomeRoute = 'public.home';
    private static $adminHomeRoute = 'public.home';
    private static $activationRoute = 'activation-required';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function getUserHomeRoute()
    {
        return self::$userHomeRoute;
    }

    public static function getAdminHomeRoute()
    {
        return self::$adminHomeRoute;
    }

    public static function getActivationRoute()
    {
        return self::$activationRoute;
    }

    public static function activeRedirect($user, $currentRoute)
    {
        if ($user->activated) {
            Log::info('Activated user attempted to visit '.$currentRoute.'. ', [$user]);

            $message = trans('auth.regThanks');
            if (config('settings.activation')) {
                $message = trans('auth.alreadyActivated');
            }

            if ($user->isAdmin()) {
                return response()->json([
                    'status'  => 'info',
                    'message' => $message,
                    'redirect' => route(self::getAdminHomeRoute()),
                ], 200);
            }

            return response()->json([
                'status'  => 'info',
                'message' => $message,
                'redirect' => route(self::getUserHomeRoute()),
            ], 200);
        }

        return false;
    }

    public function initial()
    {
        $user = Auth::user();
        $lastActivation = Activation::where('user_id', $user->id)->get()->last();
        $currentRoute = Route::currentRouteName();

        $rCheck = $this->activeRedirect($user, $currentRoute);
        if ($rCheck) {
            return $rCheck;
        }

        return response()->json([
            'status' => 'info',
            'email'  => $user->email,
            'date'   => $lastActivation->created_at->format('m/d/Y'),
        ], 200);
    }

    public function activationRequired()
    {
        $user = Auth::user();
        $lastActivation = Activation::where('user_id', $user->id)->get()->last();
        $currentRoute = Route::currentRouteName();

        $rCheck = $this->activeRedirect($user, $currentRoute);
        if ($rCheck) {
            return $rCheck;
        }

        if ($user->activated === false) {
            $activationsCount = Activation::where('user_id', $user->id)
                ->where('created_at', '>=', Carbon::now()->subHours(config('settings.timePeriod')))
                ->count();

            if ($activationsCount > config('settings.maxAttempts')) {
                Log::info('Exceeded max resends in last '.config('settings.timePeriod').' hours. '.$currentRoute.'. ', [$user]);

                return response()->json([
                    'status'  => 'error',
                    'message' => trans('auth.maxAttemptsExceeded'),
                    'email'   => $user->email,
                    'hours'   => config('settings.timePeriod'),
                ], 400);
            }
        }

        Log::info('Registered attempted to navigate while unactivated. '.$currentRoute.'. ', [$user]);

        return response()->json([
            'status' => 'error',
            'message' => trans('auth.activationPending'),
            'email'  => $user->email,
            'date'   => $lastActivation ? $lastActivation->created_at->format('m/d/Y') : null,
        ], 400);
    }

    public function activate($token)
    {
        $user = Auth::user();
        $currentRoute = Route::currentRouteName();
        $ipAddress = new CaptureIpTrait();
        $role = Role::where('slug', '=', 'user')->first();

        $rCheck = $this->activeRedirect($user, $currentRoute);
        if ($rCheck) {
            return $rCheck;
        }

        $activation = Activation::where('token', $token)->get()
            ->where('user_id', $user->id)
            ->first();

        if (empty($activation)) {
            Log::info('Registered user attempted to activate with an invalid token: '.$currentRoute.'. ', [$user]);

            return response()->json([
                'status'  => 'error',
                'message' => trans('auth.invalidToken'),
            ], 400);
        }

        $user->activated = true;
        $user->detachAllRoles();
        $user->attachRole($role);
        $user->signup_confirmation_ip_address = $ipAddress->getClientIp();
        $user->save();

        Activation::where('user_id', $user->id)->delete();

        Log::info('Registered user successfully activated. '.$currentRoute.'. ', [$user]);

        return response()->json([
            'status'  => 'success',
            'message' => trans('auth.successActivated'),
            'redirect' => $user->isAdmin() ? route(self::getAdminHomeRoute()) : route(self::getUserHomeRoute()),
        ], 200);
    }

    public function resend()
    {
        $user = Auth::user();
        $lastActivation = Activation::where('user_id', $user->id)->get()->last();
        $currentRoute = Route::currentRouteName();

        if ($user->activated === false) {
            $activationsCount = Activation::where('user_id', $user->id)
                ->where('created_at', '>=', Carbon::now()->subHours(config('settings.timePeriod')))
                ->count();

            if ($activationsCount >= config('settings.maxAttempts')) {
                Log::info('Exceeded max resends in last '.config('settings.timePeriod').' hours. '.$currentRoute.'. ', [$user]);

                return response()->json([
                    'status'  => 'error',
                    'message' => trans('auth.maxAttemptsExceeded'),
                    'email'   => $user->email,
                    'hours'   => config('settings.timePeriod'),
                ], 400);
            }

            $sendEmail = $this->initiateEmailActivation($user);

            Log::info('Activation resent to registered user. '.$currentRoute.'. ', [$user]);

            return response()->json([
                'status'  => 'success',
                'message' => trans('auth.activationSent'),
            ], 200);
        }

        Log::info('Activated user attempted to navigate to '.$currentRoute.'. ', [$user]);

        return response()->json([
            'status'  => 'info',
            'message' => trans('auth.alreadyActivated'),
            'redirect' => route(self::getUserHomeRoute()),
        ], 200);
    }

    public function exceeded()
    {
        $user = Auth::user();
        $currentRoute = Route::currentRouteName();
        $timePeriod = config('settings.timePeriod');
        $lastActivation = Activation::where('user_id', $user->id)->get()->last();
        $activationsCount = Activation::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subHours($timePeriod))
            ->count();

        if ($activationsCount >= config('settings.maxAttempts')) {
            Log::info('Locked non-activated user attempted to visit '.$currentRoute.'. ', [$user]);

            return response()->json([
                'status'    => 'error',
                'message'   => trans('auth.exceededAttempts'),
                'email'     => $user->email,
                'lastDate'  => $lastActivation->created_at->format('m/d/Y'),
                'hours'     => config('settings.timePeriod'),
            ], 400);
        }

        return response()->json([
            'status'    => 'info',
            'message'   => trans('auth.activationPending'),
            'redirect'  => route(self::getActivationRoute()),
        ], 200);
    }
}
