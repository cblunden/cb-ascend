<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ViewErrorBag;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => fn () => $this->getFlashData(),
        ];
    }

    private function getFlashData(): object
    {
        $flash = collect(session()->get('_flash.old'))->mapWithKeys(function ($key) {
            $value = session()->get($key);

            if ($value instanceof ViewErrorBag) {
                $value = $value->toArray();
            }

            return [$key => $value];
        });

        return (object) ($flash->isEmpty() ? [] : Arr::undot($flash->toArray()));
    }
}
