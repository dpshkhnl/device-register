<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycSubmitted
{
    public const MESSAGE = 'Please complete your KYC (photo, government ID and alternate number) before changing device status.';

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->hasKyc()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => self::MESSAGE], 403);
        }

        return redirect()->to(route('profile.edit').'#kyc')->with('kyc_required', self::MESSAGE);
    }
}
