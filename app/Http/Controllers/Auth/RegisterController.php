<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 15/12/2018
 * Time: 16:57
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->middleware('guest');
        $this->users = $users;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|max:190|unique:users',
            'password' => 'required|min:6|max:190|confirmed',
            'password_confirmation' => 'required|min:6|max:190',
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $validator = $this->validator($data);
        if ($validator->fails()) {
            return redirect()->route('register')->withErrors($validator);
        }
        $user = $this->users->save([
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'api_token' => str_random(60)
        ]);
        return redirect()->route('login')->with(['username' => $user->username]);
    }

}
