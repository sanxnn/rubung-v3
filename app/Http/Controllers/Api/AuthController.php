<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:100',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => 'customer',
            'email_verified_at' => null,
        ]);

        $otp = $this->createOtp(
            $user,
            'email_verification'
        );

        $this->sendOtpEmail(
            $user->email,
            $user->name,
            $otp,
            'email_verification'
        );

        $token = $user
            ->createToken('customer-token')
            ->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil. Silakan verifikasi email menggunakan OTP yang dikirim ke email Anda.',
            'data' => [
                'user' => $user,
                'token' => $token,
                'email_verified' => false,
            ],
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);

        $user = User::where(
            'email',
            $validated['email']
        )->first();

        if (
            !$user ||
            !Hash::check(
                $validated['password'],
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'Email atau password salah.',
                ],
            ]);
        }

        if (!$user->isCustomer()) {
            return response()->json([
                'message' => 'Akun bukan akun customer.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Email Verification Check
        |--------------------------------------------------------------------------
        */

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Email belum diverifikasi.',
                'data' => [
                    'email' => $user->email,
                    'email_verified' => false,
                ],
            ], 403);
        }

        $token = $user
            ->createToken('customer-token')
            ->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => [
                'user' => $user,
                'token' => $token,
                'email_verified' => true,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Me
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {
        return response()->json([
            'message' => 'Data customer berhasil diambil.',
            'data' => $request->user(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Profile
    |--------------------------------------------------------------------------
    */

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'required',
                'email',
                'max:100',
                'unique:users,email,' . $user->id,
            ],
        ]);

        $emailChanged =
            strtolower($user->email) !==
            strtolower($validated['email']);

        DB::transaction(function () use (
            $user,
            $validated,
            $emailChanged
        ) {
            $user->name =
                $validated['name'];

            $user->phone =
                $validated['phone'] ?? null;

            $user->email =
                $validated['email'];

            if ($emailChanged) {
                $user->email_verified_at = null;
            }

            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Kalau email berubah, hapus OTP lama
            |--------------------------------------------------------------------------
            */

            if ($emailChanged) {
                EmailOtp::where(
                    'user_id',
                    $user->id
                )
                    ->where(
                        'purpose',
                        'email_verification'
                    )
                    ->delete();
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Kirim OTP kalau email berubah
        |--------------------------------------------------------------------------
        */

        if ($emailChanged) {
            $otp = $this->createOtp(
                $user,
                'email_verification'
            );

            $this->sendOtpEmail(
                $user->email,
                $user->name,
                $otp,
                'email_verification'
            );
        }

        $user->refresh();

        return response()->json([
            'message' => $emailChanged
                ? 'Profile berhasil diperbarui. OTP verifikasi telah dikirim ke email baru.'
                : 'Profile berhasil diperbarui.',

            'data' => [
                'user' => $user,
                'email_verified' => $user->email_verified_at !== null,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Verify Email
    |--------------------------------------------------------------------------
    */

    public function verifyEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'otp' => [
                'required',
                'digits:6',
            ],
        ]);

        $user = User::where(
            'email',
            $validated['email']
        )->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email tidak ditemukan.',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email sudah diverifikasi.',
                'data' => [
                    'email_verified' => true,
                ],
            ]);
        }

        $otpRecord = EmailOtp::where(
            'user_id',
            $user->id
        )
            ->where(
                'purpose',
                'email_verification'
            )
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'OTP tidak ditemukan. Silakan minta OTP baru.',
            ], 422);
        }

        if ($otpRecord->isExpired()) {
            return response()->json([
                'message' => 'OTP sudah kedaluwarsa. Silakan minta OTP baru.',
            ], 422);
        }

        if ($otpRecord->attempts >= 5) {
            return response()->json([
                'message' => 'Percobaan OTP telah melebihi batas. Silakan minta OTP baru.',
            ], 429);
        }

        if (!Hash::check(
            $validated['otp'],
            $otpRecord->otp_hash
        )) {
            $otpRecord->increment('attempts');

            return response()->json([
                'message' => 'OTP tidak valid.',
            ], 422);
        }

        DB::transaction(function () use (
            $user,
            $otpRecord
        ) {
            $user->email_verified_at = now();
            $user->save();

            $otpRecord->used_at = now();
            $otpRecord->save();
        });

        return response()->json([
            'message' => 'Email berhasil diverifikasi.',
            'data' => [
                'email_verified' => true,
                'user' => $user->fresh(),
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Resend Verification OTP
    |--------------------------------------------------------------------------
    */

    public function resendVerificationOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        $user = User::where(
            'email',
            $validated['email']
        )->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email tidak ditemukan.',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email sudah diverifikasi.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus OTP lama
        |--------------------------------------------------------------------------
        */

        EmailOtp::where(
            'user_id',
            $user->id
        )
            ->where(
                'purpose',
                'email_verification'
            )
            ->delete();

        $otp = $this->createOtp(
            $user,
            'email_verification'
        );

        $this->sendOtpEmail(
            $user->email,
            $user->name,
            $otp,
            'email_verification'
        );

        return response()->json([
            'message' => 'OTP verifikasi baru telah dikirim ke email Anda.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Forgot Password
    |--------------------------------------------------------------------------
    */

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        $user = User::where(
            'email',
            $validated['email']
        )
            ->where(
                'role',
                'customer'
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Jangan bocorkan apakah email terdaftar
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            return response()->json([
                'message' => 'Jika email terdaftar, OTP reset password akan dikirim ke email tersebut.',
            ]);
        }

        EmailOtp::where(
            'user_id',
            $user->id
        )
            ->where(
                'purpose',
                'password_reset'
            )
            ->delete();

        $otp = $this->createOtp(
            $user,
            'password_reset'
        );

        $this->sendOtpEmail(
            $user->email,
            $user->name,
            $otp,
            'password_reset'
        );

        return response()->json([
            'message' => 'Jika email terdaftar, OTP reset password telah dikirim ke email Anda.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'otp' => [
                'required',
                'digits:6',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = User::where(
            'email',
            $validated['email']
        )
            ->where(
                'role',
                'customer'
            )
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Data reset password tidak valid.',
            ], 422);
        }

        $otpRecord = EmailOtp::where(
            'user_id',
            $user->id
        )
            ->where(
                'purpose',
                'password_reset'
            )
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'OTP tidak ditemukan. Silakan minta OTP baru.',
            ], 422);
        }

        if ($otpRecord->isExpired()) {
            return response()->json([
                'message' => 'OTP sudah kedaluwarsa. Silakan minta OTP baru.',
            ], 422);
        }

        if ($otpRecord->attempts >= 5) {
            return response()->json([
                'message' => 'Percobaan OTP telah melebihi batas. Silakan minta OTP baru.',
            ], 429);
        }

        if (!Hash::check(
            $validated['otp'],
            $otpRecord->otp_hash
        )) {
            $otpRecord->increment('attempts');

            return response()->json([
                'message' => 'OTP tidak valid.',
            ], 422);
        }

        DB::transaction(function () use (
            $user,
            $otpRecord,
            $validated
        ) {
            $user->password =
                $validated['password'];

            $user->save();

            $otpRecord->used_at = now();
            $otpRecord->save();

            /*
            |--------------------------------------------------------------------------
            | Logout semua device setelah password berubah
            |--------------------------------------------------------------------------
            */

            $user->tokens()->delete();
        });

        return response()->json([
            'message' => 'Password berhasil diubah. Silakan login kembali.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Create OTP
    |--------------------------------------------------------------------------
    */

    private function createOtp(
        User $user,
        string $purpose
    ): string {
        $otp = (string) random_int(
            100000,
            999999
        );

        EmailOtp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'otp_hash' => Hash::make($otp),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        return $otp;
    }


    /*
    |--------------------------------------------------------------------------
    | Send OTP Email
    |--------------------------------------------------------------------------
    */

    private function sendOtpEmail(
        string $email,
        string $name,
        string $otp,
        string $purpose
    ): void {
        $isVerification =
            $purpose === 'email_verification';

        $subject = $isVerification
            ? 'Verifikasi Email - Batik Rubung Kuning'
            : 'Reset Password - Batik Rubung Kuning';

        $title = $isVerification
            ? 'Verifikasi Email Anda'
            : 'Reset Password';

        $description = $isVerification
            ? 'Gunakan kode OTP berikut untuk memverifikasi alamat email Anda.'
            : 'Gunakan kode OTP berikut untuk mengatur ulang password akun Anda.';

        Mail::html(
            view(
                'emails.otp',
                [
                    'name' => $name,
                    'otp' => $otp,
                    'title' => $title,
                    'description' => $description,
                ]
            )->render(),
            function ($message) use (
                $email,
                $subject
            ) {
                $message
                    ->to($email)
                    ->subject($subject);
            }
        );
    }
}
