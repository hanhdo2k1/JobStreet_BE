<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Mail\ForgotPassword;
use App\Mail\RegisterEmail;
use App\Models\Company;
use App\Models\Application;
use App\Models\User;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json(
            $users
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function userLogin(Request $request)
    {
        
        if(empty($request->email)){
            return response()->json([
                "status" => "empty_email",
                'message' => 'Vui lòng nhâp email của bạn'
            ]);
        }elseif(empty($request->password)){
            return response()->json([
            "status" => "empty_password",
              'message' => 'Vui lòng nhâp mật khẩu của bạn'
            ]);
        }else{
            $user=$request->only("email","password");
            if(Auth::attempt($user)){
                // $user=new User();
                $user=Auth::user();
                // $token=JWTAuth::fromUser($user);
                $token=$user->token;
                // $user->save();
            }else{
                return response()->json([
                    "status"=>404,
                    "message"=>"Tài khoản hoặc mật khẩu sai"
                ]);
            }
            return response()->json([
                "status"=>200,
                'token' =>$token
                ]
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => "required|string",
            "avatar" => "nullable|string",
            'email' => "required|string",
            'password' => "required|string",
            "number_phone" => "required|string",
            "address" => "required|string"
        ]);
        $user =new  User();
        $user->username = $request->username;
        $user->avatar = "user.png";
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->number_phone = $request->number_phone;
        $user->address = $request->address;
        $user->save();
        if(Auth::attempt(["email"=>$request->email, "password"=>$request->password])){
            $user=Auth::user();
            $token=JWTAuth::fromUser($user);
            $user->token=$token;
            $user->save();
        }
        
        Mail::to($request->email)->send(new RegisterEmail($request->username));
        return response()->json(
            $user
        );
    }

    /**
     * Display the specified resource.
     */
    public function countEndUser()
    {
        $userCount = User::count();
        $companyCount = Company::count();
        $application = Application::count();
        $jobCount = Job::count();

        return response()->json([
            "user" => $userCount,
            "company" => $companyCount,
            "application" => $application,
            "job" => $jobCount
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($email)
    {   
        $user = User::where("email", $email)->first();
        if(!$user){
            return response()->json([
                "status"=>400,
                "message" => "Tài khoản không tồn tại"
            ]);
        }
        return response()->json(
            [ 
            "status"=>200,
            "user" => $user
            ]
           
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'username' => "required|string",
            'avatar' => "nullable",
            'number_phone' => "required|numeric",
            'address' => "required|string"
        ]);

        $id = $request->id;
        $username = $request->username;
        $phone_number = $request->number_phone;
        $address = $request->address;
        $user = User::findOrFail($id);
        if ($request->hasFile("avatar")) {
            $avatar=$request->file("avatar");
            $avatarName = Str::random(16) . "." . $request->avatar->getClientOriginalExtension();
            Storage::disk("public")->put($avatarName, file_get_contents($avatar));
            $user->avatar = $avatarName;
        }
        $user->username = $username;
        $user->number_phone = $phone_number;
        $user->address = $address;
        $user->save();
        return response()->json(
            [
                "status" => 200,
                "success" => "Thành công"
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function recoverPass(Request $request, $email)
    {
        $request->validate([
            "password" => "required|string|min:8"
        ]);
        $user = User::where("email", $email)->first();
        if (!$user) {
            return response()->json(
                "Tài khoản không tồn tại"
            );
        }
        $user->password =bcrypt($request->password);
        $user->save();
        return response()->json(
            "Thành công"
        );
    }

    public function confirmEmail(Request $request)
    {
        $confirmemail = $request->email;
        $user = User::where("email", $confirmemail)->first();
        if ($user) {
            $verificationCode =strval(rand(100000, 999999));
            Mail::to($confirmemail)->send(new ForgotPassword($verificationCode));
        }
        return response()->json(
            $verificationCode
        );
    }

    public function userChangePassword(Request $request){
            $id=$request->id;
            $password=$request->password;
            $user=User::where("id",$id)->first();
            if(!$user){
                return response()->json(
                    [
                        "status"=>400,
                        "message" => "Không thể đổi mật khẩu"
                    ]
                 
                );
            }
            $user->password =Hash::make($password);
            $user->save();
            return response()->json(
                [
                    "status"=>200,
                    "user"=>$password
                ]
            );
            
    }

    public function comparePassword(Request $request){
        $id=$request->id;
        $password=$request->password;
        $user=User::where("id",$id)->first();
        if(!Hash::check($password,$user->password) ){
            return response()->json(
                [ "status"=>400,
                    "message"=>"Mật khẩu sai"]
            );
        }
        return response()->json(
            [ 
            "status"=>200,
            ]
            );
    }

    public function deleteUsers(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['status' => 'ok', 'message' => 'Delete succeeded']);
    }

    public function getUserToken($token){
        $user = User::where("token", $token)->first();
        if(!$user){
            return response()->json([
                "status"=>400,
                "message" => "Tài khoản không tồn tại"
            ]);
        }
        return response()->json(
            [ 
            "status"=>200,
            "user" => $user
            ]
           
        );
    }


}


