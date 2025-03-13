<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // چک کردن توکن از کوکی
            $token = $request->cookie('auth_token');

            if (! $token) {
                // اگه توکن توی کوکی نبود، از هدر بخون
                $token = $request->bearerToken();
            }

            if (! $token) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'توکن ارائه نشده است',
                    'data'    => null,
                ], 401);
            }

            // اعتبارسنجی توکن
            $user = JWTAuth::setToken($token)->authenticate();
            if (! $user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'کاربر یافت نشد',
                    'data'    => null,
                ], 401);
            }

            // ذخیره کاربر توی درخواست برای استفاده بعدی
            $request->attributes->add(['user' => $user]);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'توکن منقضی شده است',
                'data'    => null,
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'توکن نامعتبر است',
                'data'    => null,
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطا در پردازش توکن',
                'data'    => null,
            ], 401);
        }

        return $next($request);
    }
}
