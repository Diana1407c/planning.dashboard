<?php

namespace App\Http\Middleware;

use App\Services\Security\JWTAuth;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    protected string $rootPath = 'admin';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            //
        ]);
    }

    /**
     * Handle the incoming request.
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $rootView = 'custom')
    {
        $this->rootView = $this->rootPath.'/'.$rootView;
        Inertia::version(function () use ($request) {
            return $this->version($request);
        });

        Inertia::share($this->share($request));
        Inertia::setRootView($this->rootView($request));

        $response = $next($request);
        $response->headers->set('Vary', 'X-Inertia');

        if (! $request->header('X-Inertia')) {
            return $response;
        }

        if ($request->method() === 'GET' && $request->header('X-Inertia-Version', '') !== Inertia::getVersion()) {
            $response = $this->onVersionChange($request, $response);
        }

        if ($response->isOk() && empty($response->getContent())) {
            $response = $this->onEmptyResponse($request, $response);
        }

        if ($response->getStatusCode() === 302 && in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            $response->setStatusCode(303);
        }

        return $response;
    }
}
