<?php

namespace App\Http\Controllers;

use App\Mail\NotificationToCandidate;
use App\Mail\NotificationToEmployee;
use App\Mail\StatusApplicationNotification;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applications = Application::all();
        return response()->json($applications);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "token" => "required|string",
            "job_id" => "required|numeric",
            "cv" => "nullable",
            "name" => "required",
            "cover_letter" => "nullable",
            "position" => "required",
            "status" => "required",
        ]);

        $token = $request->token;
        $job_id = $request->job_id;
        $name = $request->name;
        $cover_letter = $request->cover_letter;
        $position = $request->position;
        $status = "Chờ xử lý";

        $user = User::where("token", $token)->first();
        $exitUser=Application::where("user_id", $user->id)
        ->where("job_id", $job_id)
        ->first();
        if($exitUser){
            return response()->json(
                [
                    "status" => 400,
                    "message" => "Bạn đã nộp đơn"
                ]
                );
        }
        $application = new Application();
        $application->user_id = $user->id;
        $application->job_id = $job_id;
        $fileName = Str::random(20) . "." . $request->cv->getClientOriginalExtension();
        if ($request->hasFile("cv")) {
            Storage::disk("public")->put($fileName, file_get_contents($request->cv));
            $application->cv = $fileName;
        }
        $application->status = $status;
        $application->save();

        $job = Job::join("companies", "jobs.company_id", "=", "companies.id")
            ->select("companies.*")
            ->where("jobs.id", $job_id)->first();
        if ($application) {
            Mail::to($job->email)->send(new NotificationToEmployee($name, $cover_letter, $fileName, $job->company_name));
            Mail::to($user->email)->send(new NotificationToCandidate($name, $job->company_name));
            return response()->json([
                "status" => 200,
                "name" => $name,
                "cover_letter" => $cover_letter,
                "position" => $position,
            ]);
        }
        return response()->json("Thất bại");
    }


    /**
     * Display the specified resource.
     */
    public function show($token)
    {
        $apply = Job::join("companies", "jobs.company_id", "=", "companies.id")
            ->join("applications", "jobs.id", "=", "applications.job_id")
            ->join("users", "applications.user_id", "=", "users.id")
            ->select("companies.company_name", "jobs.position", "applications.status", "applications.created_at")
            ->where("users.token", $token)
            ->get();
        return response()->json($apply);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }



    public function getCV($name)
    {
        $application = Application::where('cv', $name)->first();

        if ($application && $application->cv) {
            $path = storage_path('app/public/' . $name);
            return response()->file($path);
        } else {
            return response()->json('CV not found', 404);
        }
    }

    public function getApplicationsByJob($job_id)
    {
        // Xử lý logic để lấy danh sách applications dựa trên job_id
        $applications = Application::where('job_id', $job_id)->get();

        return response()->json($applications);
    }


public function getApplicationByCompany($token)
{
   
    $applications = DB::table('applications')
        ->select('applications.created_at', 'applications.status', 'applications.user_id','applications.job_id','applications.cv', 'users.email', 'users.username' ,'jobs.position')
        ->join('users', 'users.id', '=', 'applications.user_id')
        ->join('jobs', 'jobs.id', '=', 'applications.job_id')
        ->join('companies', 'companies.id', '=', 'jobs.company_id')
        ->where('companies.token', $token)
        ->get();


        return response()->json($applications);
    }



   
public function ChangeStatusApplication(Request $request, $user_id, $job_id)
{
    $status = $request->input('status');


        $result = DB::table('applications')
            ->where('user_id', $user_id)
            ->where('job_id', $job_id)
            ->update(['status' => $status]);

        if ($result) {
            $user = User::find($user_id);
            $job = Job::find($job_id);
            $company = Company::find($job->company_id); // Lấy thông tin công ty từ bảng companies

            $email = new StatusApplicationNotification($user, $job, $status, $company);
            Mail::to($user->email)->send($email);

            return response()->json("Cập nhật thành công và đã gửi email");
        } else {
            return response()->json("Lỗi khi cập nhật", 500);
        }
    }

    public function RejectApplication(Request $request, $user_id, $job_id)
    {
        $status = 'rejected';

        $result = DB::table('applications')
            ->where('user_id', $user_id)
            ->where('job_id', $job_id)
            ->update(['status' => $status]);

        if ($result) {
            $user = User::find($user_id);
            $job = Job::find($job_id);
            $company = Company::find($job->company_id);

            $email = new StatusApplicationNotification($user, $job, $status, $company);
            Mail::to($user->email)->send($email);

            return response()->json("Cập nhật thành công và đã gửi email");
        } else {
            return response()->json("Lỗi khi cập nhật", 500);
        }
    }
}




