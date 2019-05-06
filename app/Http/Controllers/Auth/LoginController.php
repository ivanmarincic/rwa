<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 15/12/2018
 * Time: 14:11
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    private $users;

    /**
     * LoginController constructor.
     * @param UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->users = $users;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|max:190|exists:users',
            'password' => 'required|min:6|max:190'
        ]);
    }

    protected function login(Request $request)
    {
        $data = $request->all();
        $validator = $this->validator($data);
        if ($validator->fails()) {
            return redirect()->route('login')->withErrors($validator);
        }
        $user = $this->users->getByUsername($request->input('username'));
        if (!is_null($user) && Hash::check($request->input('password'), $user->password)) {
            if (Hash::needsRehash($user->password)) {
                $user->password = Hash::make($request->input('password'));
                $this->users->update($user, $user->id);
            }
            Auth::login($user);
            return redirect()->route('login');
        } else {
            $validator->errors()->add('password', __('auth.failed'));
            return redirect()->route('login')
                ->withErrors($validator)
                ->with(['username' => $request->input('username')]);
        }
    }

    protected function loginApi(Request $request)
    {
        $data = $request->all();
        $validator = $this->validator($data);
        if ($validator->fails()) {
            return response("Invalid login", 400);
        }
        $user = $this->users->getByUsername($request->input('username'));
        if (!is_null($user) && Hash::check($request->input('password'), $user->password)) {
            if (Hash::needsRehash($user->password)) {
                $user->password = Hash::make($request->input('password'));
                $this->users->update($user, $user->id);
            }
            Auth::login($user);
            $userJson = $user->toArray();
            $userJson["api_token"] = $user["api_token"];
            return response($userJson, 200);
        } else {
            return response("Invalid login", 403);
        }
    }

    protected function logout()
    {
        Auth::logout();
        return redirect("/mi-chat/login");
    }
}
