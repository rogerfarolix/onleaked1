<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'              => $request->user()->id,
                    'name'            => $request->user()->name,
                    'email'           => $request->user()->email,
                    'role'            => $request->user()->role,
                    'alert_frequency' => $request->user()->alert_frequency ?? 'instant',
                    'email_verified'  => !is_null($request->user()->email_verified_at),
                ] : null,
            ],
            'flash' => [
                'success' => session('success'),
                'error'   => session('error'),
                'status'  => session('status'),
            ],
            'ziggy' => fn() => array_merge((new Ziggy)->toArray(), ['location' => $request->url()]),
        ]);
    }
}
