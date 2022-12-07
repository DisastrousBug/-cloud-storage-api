<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class TokenAuthController extends Controller
{
    /**
     *
     * Issue token for device
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {

        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required',
            ]
        );

        $user = User::where('email', $request->email)->first();

//        if ($user && is_null($user->password)) {
//            $status = (Password::broker(config('fortify.passwords')))->sendResetLink(['email' => $user->email]);
//        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(
                [
                    'email' => ['The provided credentials are incorrect.'],
                ]
            );
        }

        return response()->json(
            ['token' => $user->createToken($request->device_name)->plainTextToken]
        );
    }

    /**
     *
     * Deletes all user tokens
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json();
    }
}
