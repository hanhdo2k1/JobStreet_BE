<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Company;
use App\Models\Job;
use App\Traits\ApiResponseWithHttpStatus;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobAddAdmin;

class JobApiController extends Controller
{
    use ApiResponseWithHttpStatus;

    // public function index()
    // {
    //     $data['categories'] = Categories::all();

    //     return $this->apiResponse('success', $data, Response::HTTP_OK, true);
    // }

    public function getOpenedJob()
    {
        $data['jobs'] = Job::select('position', 'type', 'status', 'salary', 'id', 'description', 'close_day', 'company_id')
            ->with('company:id,company_name,address,logo')
            ->where('status', 'Open')
            ->get();

        return $this->apiResponse('success', $data, Response::HTTP_OK, true);
    }

    public function getJobDetails($id)
    {
        $detail = Job::where('jobs.id', $id)
            ->join("companies", 'jobs.company_id', '=', 'companies.id')
            ->select("jobs.id", "jobs.position", "jobs.type", "jobs.salary", "jobs.status", "jobs.description", "jobs.close_day", "companies.company_name", "companies.address", "companies.logo")
            ->first();
        return response()->json(
            [
                "status" => true,
                "job" => $detail
            ]
        );
       
    }

    public function addJob(Request $request)
    {
        $token = $request->token;
        $company = Company::where("token", $token)->first();
        $position = $request->input('position');
        $cat_id = $request->input('cat_id');
        $status = $request->input('status', 'Waiting');
        $description = $request->input('description');
        $salary = $request->input('salary');
        $type = $request->input('type');
        $closeDay = $request->input('close_day');

        // Thực hiện logic để thêm công việc vào cơ sở dữ liệu
        $job = new Job();
        $job->company_id = $company->id;
        $job->position = $position;
        $job->cat_id = $cat_id;
        $job->status = $status;
        $job->description = $description;
        $job->salary = $salary;
        $job->type = $type;
        $job->close_day = $closeDay;
        $job->save();
        Mail::to('vien.nguyen24@student.passerellesnumeriques.org')->send(new JobAddAdmin($position, $type, $description, $salary, $closeDay, $status, ));


        return response()->json($job);
    }
    public function updateJob(Request $request, $id)
    {
        // Lấy thông tin công việc từ request
        $position = $request->input('position');
        $status = $request->input('status', 'waiting');
        $description = $request->input('description');
        $salary = $request->input('salary');
        $type = $request->input('type');
        $closeDay = $request->input('close_day');

        // Tìm công việc theo ID
        $job = Job::find($id);

        if (!$job) {
            return response()->json(['error' => 'Không tìm thấy công việc']);
        }

        // Cập nhật thông tin công việc
        $job->position = $position;
        $job->status = $status;
        $job->description = $description;
        $job->salary = $salary;
        $job->type = $type;
        $job->close_day = $closeDay;
        $job->save();

        return response()->json([
            "status" => 200,
            'data' => $job
        ]);
    }

    public function deleteJob($id)
    {
        // Tìm và xóa công việc theo ID
        $job = Job::find($id);

        if (!$job) {
            return response()->json(['error' => 'Không tìm thấy công việc']);
        }

        $job->delete();

        return response()->json(['message' => 'Công việc đã được xóa thành công']);


    }

}