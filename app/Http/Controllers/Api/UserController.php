<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(['You are not allowed to get users data', 403]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return UserResource
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        return UserResource::make((new CreateNewUser())->create($request->toArray()));
    }

    /**
     * Display the specified resource.
     *
     * @param  Request $request
     *
     * @return UserResource
     */
    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'current_password' => 'required_with:password',
            'first_name' => 'nullable|max:50',
            'last_name' => 'nullable|max:50',
            'password' => 'sometimes|required|string',
            'password_confirmation' => 'required_with:password|same:password',
        ])->validated();

        $user = $request->user();
        $user->name = $data['name'];
        $user->save();

        $response = ['user' => new UserResource($user)];

        //Note Null used for case when Social User will be integreated
        if (!is_null($password = $data['password'] ?? null)) {
            (new UpdateUserPassword())->update($user, $data);

            $user->tokens()->delete();
            $response['token'] = $user->createToken('user')->plainTextToken;
        }

        return response()->json($response);
    }

    public function setPassword(Request $request)
    {
        $data = Validator::make($request->input(), [
            'password' => 'required|string',
            'password_confirmation' => 'required_with:password|same:password',
        ])->validated();

        $user = $request->user();

        (new ResetUserPassword())->reset($user, $data);
        $user->refresh();

        return new UserResource($user);
    }

    public function avatarStore(){

    }
}
