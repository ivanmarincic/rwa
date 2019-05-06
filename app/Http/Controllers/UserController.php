<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 19/12/2018
 * Time: 01:17
 */

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function search(Request $request)
    {
        return $this->users->search($request->get('searchQuery'));
    }

    public function getLoggedInUser()
    {
        return Auth::user();
    }

    public function setProfileImage(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'image' => 'max:4000'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $filename = basename($request->file('image')->store('profile-images'));
        $this->users->update(['profile_image' => $filename], Auth::user()->id);
        return response($filename, 200);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->all();
        $userToUpdate = ["full_name" => $data["full_name"], "email" => $data["email"],];
        if ($request->get('change_password', "false") == "true") {
            $validator = Validator::make($data, [
                'password' => 'required|min:6|max:190|confirmed',
                'password_confirmation' => 'required|min:6|max:190',
            ]);
            if ($validator->fails()) {
                return redirect()->route('profile')->withErrors($validator);
            }
            $userToUpdate["password"] = bcrypt($data['password']);
        }
        $this->users->update($userToUpdate, Auth::user()->id);
        return redirect()->route('home');
    }

    public function updateProfileApi(Request $request)
    {
        $data = $request->all();
        $userToUpdate = ["full_name" => $data["full_name"], "email" => $data["email"],];
        if ($request->get('change_password', "false") == "true") {
            $validator = Validator::make($data, [
                'password' => 'required|min:6|max:190'
            ]);
            if ($validator->fails()) {
                return $validator->errors();
            }
            $userToUpdate["password"] = bcrypt($data['password']);
        }
        $this->users->update($userToUpdate, Auth::user()->id);
        return Auth::user();
    }

    public static function getProfileImage($filename)
    {
        if ($filename == "default_user.png") {
            return Storage::disk('local')->path($filename);
        } else {
            return Storage::disk('profile')->path($filename);
        }
    }
}
