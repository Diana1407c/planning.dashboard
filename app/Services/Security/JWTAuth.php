<?php

namespace App\Services\Security;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class JWTAuth
{
    private static ?User $user = null;

    private static string $header;
    private static string $payload;
    private static string $signature;
    private static string $expiration;

    public static function attempt(array $credentials): ?string
    {
        $user = User::query()->whereEmail($credentials['email'])->first();

        if($user && Hash::check($credentials['password'], $user->password)){
            static::$user = $user;
            return static::generate($user);
        }

        return null;
    }

    public static function generate($payload): string
    {
        $instance = new self();

        $payload['exp'] = time()+(config('jwt.time')*60);
        $headers_encoded = $instance->base64url_encode(json_encode(config('jwt.headers')));
        $payload_encoded = $instance->base64url_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", config('jwt.secret'), true);
        $signature_encoded = $instance->base64url_encode($signature);

        return "$headers_encoded.$payload_encoded.$signature_encoded";
    }

    public static function setUserFromToken($jwt): void
    {
        $tokenParts = explode('.', $jwt);
        static::$header = base64_decode($tokenParts[0]);
        static::$payload = base64_decode($tokenParts[1]);
        static::$signature = $tokenParts[2];

        $userPayload = json_decode(static::$payload);
        static::$expiration = $userPayload->exp;
        $user = User::find($userPayload->id);

        if($user){
            static::$user = $user;
        } else {
            static::$user = null;
        }
    }

    public static function validate($permission): bool
    {
        if(!static::user()){
            return false;
        }

        if(static::$expiration < time()){
            return false;
        }

        $base64_url_header = static::base64url_encode(static::$header);
        $base64_url_payload = static::base64url_encode(static::$payload);
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, config('jwt.secret'), true);
        $base64_url_signature = static::base64url_encode($signature);

        if($base64_url_signature !== static::$signature){
            return false;
        }

        return match ($permission) {
            'is_admin' => static::user()->role == 'admin',
            'is_pm' => in_array(static::user()->role, ['admin', 'project_manager']),
            'is_tl' => in_array(static::user()->role, ['admin', 'project_manager', 'team_lead']),
            default => true
        };
    }

    public static function base64url_encode($str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    public static function user(): ?User
    {
        return self::$user;
    }
}
