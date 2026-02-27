<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class UserAuthController extends Controller
{
    use apiresponse;

    // -------------------------
    // LOGIN
    // -------------------------
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Helper::jsonErrorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();
        if ($user && !$user->hasRole('user')) {
            \Log::warning('API login attempted by non-user role: ' . $request->email);
            return Helper::jsonErrorResponse('Access denied. Only user accounts can login via API.', 403);
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            \Log::warning('Login failed for: ' . $request->email);
            return Helper::jsonErrorResponse('Unauthorized. Please check your credentials.', 401);
        }

        $user = auth('api')->user();

        return $this->respondUserWithToken($user, $token, 'Login successful.');
    }

    // -------------------------
    // REGISTRATION
    // -------------------------
    public function customerRegister(Request $request)
    {
        return $this->registerUser($request, 'user');
    }

    public function ownerRegister(Request $request)
    {
        return $this->registerUser($request, 'owner', true);
    }

    protected function registerUser(Request $request, $role, $requireCategory = false)
    {
        if (strtolower($role) === 'admin') {
            return $this->error([], 'Admin registration is not allowed.', 403);
        }

        $rules = [
            'name'         => 'required|string|max:255',
            'surname'      => 'nullable|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'password'     => 'required|string|min:6',
            'phone'        => 'nullable|string|max:255',
            'address'      => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
        ];

        if ($requireCategory) {
            $rules['category_id'] = 'required|integer|exists:categories,id';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'surname', 'email', 'password', 'category_id', 'username', 'phone', 'address', 'company_name']);
            $data['username'] = $this->generateUniqueUsername($request->name);
            $data['password'] = Hash::make($request->password);

            $user = User::create($data);
            $user->assignRole($role);

            $this->sendOtp($user);

            DB::commit();

            $token = JWTAuth::fromUser($user);

            $response = [
                'user'  => $this->formatUser($user),
                'role'  => $role,
                'token' => $this->respondWithToken($token),
            ];

            return $this->success($response, 'Registered successfully.', 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 400);
        }
    }

    // -------------------------
    // FORGOT / RESET FLOW
    // -------------------------
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->email)->first();
        $this->clearPasswordResetCache();
        $this->sendOtp($user);

        Cache::put('password_reset_user_id', $user->id, now()->addMinutes(15));
        Cache::put('password_reset_otp', $user->otp, now()->addMinutes(15));
        Cache::put('password_reset_email', $user->email, now()->addMinutes(15));

        \Log::info('Forget password initiated:', ['email' => $user->email]);

        return $this->success([], 'OTP sent successfully. Please check your email.', 200);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric|digits:4',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        $userId = Cache::get('password_reset_user_id');
        $cachedOtp = Cache::get('password_reset_otp');

        if (!$userId || !$cachedOtp) {
            return $this->error([], 'Please request OTP first.', 400);
        }

        if ($request->otp != $cachedOtp) {
            return $this->error([], 'Invalid OTP.', 400);
        }

        $user = User::find($userId);
        if (!$user) return $this->error([], 'User not found.', 404);

        if ($user->otp_created_at && now()->gt(Carbon::parse($user->otp_created_at)->addMinutes(15))) {
            return $this->error([], 'OTP has expired.', 400);
        }

        Cache::put('password_reset_verified', true, now()->addMinutes(2));
        Cache::put('verified_user_id', $userId, now()->addMinutes(2));

        \Log::info('OTP verified:', ['user_id' => $userId]);

        return $this->success([], 'OTP verified successfully. You can now reset your password.', 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        $userId = Cache::get('verified_user_id');
        $isVerified = Cache::get('password_reset_verified');

        if (!$userId || !$isVerified) {
            return $this->error([], 'Please verify OTP first.', 400);
        }

        $user = User::find($userId);
        if (!$user) return $this->error([], 'User not found.', 404);

        $newPasswordHash = Hash::make($request->password);

        $updateResult = DB::table('users')
            ->where('id', $userId)
            ->update([
                'password' => $newPasswordHash,
                'otp' => null,
                'otp_created_at' => null,
                'updated_at' => now(),
            ]);

        if (!$updateResult) {
            return $this->error([], 'Password reset failed.', 500);
        }

        $this->invalidateUserTokens($userId);
        $this->clearPasswordResetCache();

        return $this->success([], 'Password reset successfully. Please login with your new password.', 200);
    }

    public function resendOtp()
    {
        $userId = Cache::get('password_reset_user_id');

        if (!$userId) {
            return $this->error([], 'No OTP request found. Please request OTP first.', 400);
        }

        $user = User::find($userId);
        if (!$user) return $this->error([], 'User not found.', 404);

        $this->sendOtp($user);
        Cache::put('password_reset_otp', $user->otp, now()->addMinutes(15));

        return $this->success([], 'OTP resent successfully. Please check your email.', 200);
    }

    // -------------------------
    // LOGOUT
    // -------------------------
    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) return $this->error([], 'Token not provided', 401);

            JWTAuth::invalidate($token);

            return $this->success([], 'Successfully logged out.', 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->error([], 'Token expired.', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->error([], 'Failed to logout: ' . $e->getMessage(), 500);
        }
    }

    // -------------------------
    // SHOW USER
    // -------------------------
    public function showUser()
    {
        try {
            $user = auth()->user() ?? JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->error([], 'User not found', 404);
            }

            $filteredUser = $user->only([
                'id',
                'avatar',
                'username',
                'surname',
                'name',
                'country',
                'city',
                'postcode',
                'email',
                'email_verified_at',
                'phone',
                'about_me',
                'description',
                'address',
            ]);

            return $this->success(['user' => $this->formatUser($user)], 'User found', 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->error([], 'Token expired', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->error([], 'Token invalid', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->error([], 'Token absent', 401);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    // -------------------------
    // UPDATE PROFILE
    // -------------------------
    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user() ?? JWTAuth::parseToken()->authenticate();

            if (!$user || !$user->id) {
                return $this->error([], 'User not found or unauthorized.', 404);
            }

            // Convert boolean-like values to booleans before validation
            $booleanFields = ['messages', 'notification'];
            foreach ($booleanFields as $field) {
                if ($request->has($field)) {
                    // filter_var returns true/false or null on failure; we leave null as-is
                    $request->merge([
                        $field => filter_var($request->input($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                    ]);
                }
            }

            $rules = [
                'full_name'    => 'nullable|string|max:255',
                'email'        => ['nullable','email', Rule::unique('users','email')->ignore($user->id,'id')],
                'phone'        => ['nullable','string','max:20', Rule::unique('users','phone')->ignore($user->id,'id')],
                'country'      => 'nullable|string|max:255',
                'city'         => 'nullable|string|max:255',
                'postcode'     => 'nullable|string|max:20',
                'avatar'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'messages'     => 'nullable|boolean',
                'notification' => 'nullable|boolean',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                \Log::info('Validation failed:', $validator->errors()->toArray());
                return $this->error([], $validator->errors(), 422);
            }

        //   // Avatar upload
        //     if ($request->hasFile('avatar')) {
        //         $file = $request->file('avatar');
        //         $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        //         $file->move(public_path('uploads/avatar'), $filename);

        //         if ($user->avatar && file_exists(public_path('uploads/avatar/' . $user->avatar))) {
        //             @unlink(public_path('uploads/avatar/' . $user->avatar));
        //         }

        //         $user->avatar = $filename;
        //     }

                // Avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Save file
            $file->move(public_path('uploads/avatar'), $filename);

            // Delete old avatar only if it is a local file (not a full URL)
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                $oldPath = public_path('uploads/avatar/' . $user->avatar);

                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Save only file name in DB
            $user->avatar = $filename;
        }


            // Full name handling
            if ($request->filled('full_name')) {
                $parts = explode(' ', trim($request->full_name), 2);
                $user->name = $parts[0];
                $user->surname = $parts[1] ?? null;
                $user->full_name = $request->full_name;
            }

            // Update other fields — use has() so boolean false isn't ignored
            $updatable = ['email', 'phone', 'country', 'city', 'postcode', 'messages', 'notification'];
            foreach ($updatable as $field) {
                if ($request->has($field)) {
                    $user->$field = $request->input($field);
                }
            }

            $user->save();

            return $this->success(['user' => $this->formatUser($user)], 'Profile updated successfully.', 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->error([], 'Token expired.', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->error([], 'Token invalid.', 401);
        } catch (\Exception $e) {
            \Log::error('Update profile error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->error([], $e->getMessage(), 500);
        }
    }

    // -------------------------
    // PASSWORD CHANGE
    // -------------------------
    public function changePassword(Request $request)
    {
        try {
            $user = auth()->user() ?? JWTAuth::parseToken()->authenticate();
            if (!$user) return $this->error([], 'User not found or unauthorized.', 404);

            $validator = Validator::make($request->all(), [
                'current_password'      => 'required|string',
                'new_password'          => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors(), 422);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return $this->error([], 'Current password is incorrect.', 400);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            // Optionally invalidate tokens (if desired)
            $this->invalidateUserTokens($user->id);

            return $this->success([], 'Password changed successfully.', 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->error([], 'Token expired.', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->error([], 'Token invalid.', 401);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    // -------------------------
    // HELPERS
    // -------------------------
    protected function sendOtp(User $user)
    {
        $otp = rand(1000, 9999);

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'otp' => $otp,
                'otp_created_at' => now(),
            ]);

        $user->refresh();

        Mail::raw("Your OTP code is: $otp. It will expire in 15 minutes.", function ($message) use ($user) {
            $message->to($user->email)->subject('Your OTP Code');
        });

        \Log::info('OTP sent:', ['email' => $user->email, 'otp' => $otp]);
    }

    protected function invalidateUserTokens($userId)
    {
        try {
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
            }

            // Optional: if you store tokens in a jwt_tokens table (custom), remove them
            if (Schema::hasTable('jwt_tokens')) {
                DB::table('jwt_tokens')->where('user_id', $userId)->delete();
            }

            \Log::info('Tokens invalidated for user: ' . $userId);
        } catch (\Exception $e) {
            \Log::warning('Token invalidation note: ' . $e->getMessage());
        }
    }

    protected function clearPasswordResetCache()
    {
        $keys = [
            'password_reset_user_id',
            'password_reset_otp',
            'password_reset_email',
            'password_reset_verified',
            'verified_user_id',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    protected function respondWithToken($token)
    {
        // token is a string; expires_in in seconds
        $ttl = null;
        try {
            $ttl = JWTAuth::factory()->getTTL(); // minutes
        } catch (\Exception $e) {
            $ttl = null;
        }

        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $ttl ? ($ttl * 60) : null,
        ];
    }

    protected function respondUserWithToken($user, $token, $message)
    {
        return $this->success([
            'role'  => $user->getRoleNames()->first(),
            'token' => $this->respondWithToken($token),
            'user'  => $this->formatUser($user),
        ], $message, 200);
    }

    protected function formatUser($user)
    {
        $category = $user->category;

        $data = [
            'id'           => $user->id,
            'avatar'       => $user->avatar ? asset('uploads/avatar/' . $user->avatar) : null,
            'name'         => $user->name,
            'full_name'    => $user->full_name,
            'surname'      => $user->surname,
            'username'     => $user->username,
            'email'        => $user->email,
            'phone'        => $user->phone,
            'country'      => $user->country ?? null,
            'city'         => $user->city ?? null,
            'postcode'     => $user->postcode ?? null,
            'messages'     => (bool) ($user->messages ?? false),
            'notification' => (bool) ($user->notification ?? false),
        ];

        if ($category) {
            $data['category_id'] = $category->id;
            $data['category_name'] = $category->name;
        }

        return $data;
    }

    protected function generateUniqueUsername($name)
    {
        $username = Str::slug($name);
        $count = User::where('username', 'LIKE', "{$username}%")->count();
        return $count > 0 ? "{$username}-{$count}" : $username;
    }
}
