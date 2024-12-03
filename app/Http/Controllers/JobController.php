<?php

namespace App\Http\Controllers;
use App\Mail\AdminBrowseInformation;
use App\Mail\Noticetothecompany;
use App\Models\Job;
use App\Models\User;
use Carbon\PHPStan\Macro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class JobController extends Controller
{

public function getJob()
{
    $job= DB::table('jobs')->get();
    return response()->json($job);
}
public function getStatus()
{
    $job=Job::select('jobs.position','jobs.status','jobs.salary','companies.company_name','jobs.type','jobs.id')
    ->where("status","=","Waiting")
    ->join('companies', 'jobs.company_id', '=', 'companies.id')
    ->get();

    return response()->json($job);
}


public function deletejob( Request $request )
{
    $company=Job::select('companies.company_name',"companies.email")
    ->where("jobs.id",$request->id)
    ->join('companies', 'jobs.company_id', '=', 'companies.id')
    ->first();
    $job=Job::find($request->id);
    if(!$job){
        return response()->json(['error' => 'Company not found'], 404);
    }
    $job->delete();

    Mail::to($company->email)->send(new Noticetothecompany("Từ chối",$company->company_name));
    return response()->json(['status' => 'ok', 'message' => 'Delete succeeded']);

}


public function Updatestatus(Request $request)
{
    $id = $request->id;
    $status = 'Open';
    $user=User::all();
    $company=Job::select('companies.company_name',"companies.email")
    ->where("jobs.id",$id)
    ->join('companies', 'jobs.company_id', '=', 'companies.id')
    ->first();
    $job = Job::findOrFail($id);
    $job->status = $status;
    $job->save();
    Mail::to($company->email)->send(new Noticetothecompany("Chấp nhận",$company->company_name));
    // foreach ($user as $value) {
    //     Mail::to($value->email)->send(new AdminBrowseInformation($value->username,$company->company_name,$job->position));
    // }
    return response()->json(
        "Cập nhật thành công"
    );
}

}
