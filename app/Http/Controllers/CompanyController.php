<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Mail\ForgotPassword;
use App\Mail\RegisterEmail;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\GoogleCalendar\Event;
use App\Models\Job;
use Carbon\Carbon;
use Spatie\GoogleCalendar\GoogleCalendarFactory;
use Illuminate\Support\Facades\DB;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Company::all();
        return response()->json(
            $company
        );
    }


    public function selectData()
    {
        $results = Company::leftJoin('jobs', 'companies.id', '=', 'jobs.company_id')
            ->select(
                DB::raw('SUM(CASE WHEN jobs.status = "open" THEN 1 ELSE 0 END) AS count'),
                'companies.id',
                'companies.company_name',
                'companies.logo',
                'companies.address',
                'companies.number_phone',
                DB::raw('GROUP_CONCAT(CASE WHEN jobs.status = "open" THEN jobs.position ELSE "Không có vị trí nào đang tuyển" END) AS positions')
            )
            ->where('jobs.status', 'open')
            ->groupBy('companies.id', 'companies.company_name', 'companies.logo', 'companies.address', 'companies.number_phone', 'companies.id')
            ->get();

        return response()->json($results);
    }



    public function getCompany(Request $request, $companyId)
    {
        $results = Company::leftJoin('jobs', 'companies.id', '=', 'jobs.company_id')
            ->select(
                DB::raw('SUM(CASE WHEN jobs.status = "open" THEN 1 ELSE 0 END) AS count'),
                'companies.id',
                'companies.company_name',
                'companies.website',
                'companies.number_phone',
                'companies.description',
                'companies.scale',
                'companies.logo',
                "companies.address",
                'jobs.id as job_id',

                'jobs.salary',
                'jobs.type',
                'jobs.close_day',
                DB::raw('GROUP_CONCAT(CASE WHEN jobs.status = "open" THEN jobs.position ELSE "Không có vị trí nào đang tuyển" END) AS position')
            )
            ->where('companies.id', '=', $companyId)
            ->groupBy(
                'companies.id',
                "companies.address",
                'companies.company_name',
                'companies.logo',
                'companies.scale',
                'companies.website',
                'companies.number_phone',
                'companies.description',
                'companies.id',
                'jobs.salary',
                'jobs.id',
                "jobs.type",
                "jobs.close_day"
            )
            ->get();

        return response()->json($results);
    }
    public function deleteadminCompany(Request $request ,$companyId)
    {
        $company = Company::find($companyId);

        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        $company->delete();

        return response()->json(['status' => 'ok', 'message' => 'Delete succeeded']);
    }





    // ---------------------------------------------------------------

    /**
     * Show the form for creating a new resource.
     */
    public function EmployeeLogin(Request $request)
    {

        if (empty($request->email)) {
            return response()->json([
                "status" => "empty_email",
                'message' => 'Vui lòng nhâp email của bạn'
            ]);
        } elseif (empty($request->password)) {
            return response()->json([
                "status" => "empty_password",
                'message' => 'Vui lòng nhâp mật khẩu của bạn'
            ]);
        } else {
            $companies = $request->only("email", "password");
            if (Auth::guard('companies')->attempt($companies)) {
                $company = Auth::guard('companies')->user();
                $token = $company->token;
            } else {
                return response()->json([
                    "status" => 404,
                    "message" => "Tài khoản hoặc mật khẩu sai"
                ]);
            }
            return response()->json(
                [
                    "status" => 200,
                    'token' => $token
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
            'company_name' => "required|string",
            'logo' => "nullable|string",
            "scale" => "required|string",
            "description" => "required|string",
            "website" => "required|string",
            'email' => "required|string",
            'password' => "required|string",
            'address' => "required|string",
            'number_phone' => "required|numeric"
        ]);

        $company = new Company();
        $company->company_name = $request->company_name;
        $company->logo = "company.png";
        $company->scale = $request->scale;
        $company->description = $request->description;
        $company->website = $request->website;
        $company->email = $request->email;
        $company->password = bcrypt($request->password);
        $company->address = $request->address;
        $company->number_phone = $request->number_phone;
        $company->save();

        if (Auth::guard('companies')->attempt(["email" => $request->email, "password" => $request->password])) {
            $company = Auth::guard('companies')->user();
            $token = JWTAuth::fromUser($company);
            $company->token = $token;
            $company->save();
        }

        Mail::to($request->email)->send(new RegisterEmail($request->company_name));
        return response()->json(
            $company
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($email)
    {
        $company = Company::where("email", $email)->first();

        if (!$company) {
            return response()->json([
                "status" => 400,
                "message" => "Tài khoản không tồn tại"
            ]);
        }
        return response()->json(
            [
                "status" => 200,
                "user" => $company

            ]


        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateCompanyInfo(Request $request)
    {
        $request->validate([
            'company_name' => "required|string",
            'logo' => "nullable",
            'scale' => "required|string",
            'description' => "required|string",
            'website' => "required|string",
            'address' => "required|string",
            'number_phone' => "required|numeric",
        ]);
            $id = $request->id;
            $company_name = $request->company_name;
            $logo = $request->logo;
            $scale = $request->scale;
            $website = $request->website;
            $address = $request->address;
            $number_phone = $request->number_phone;
            $company = Company::findOrFail($id);
            if ($request->hasFile("logo")) {
                $logo = $request->file("logo");
                $logoName = Str::random(16) . "." . $request->logo->getClientOriginalExtension();
                Storage::disk("public")->put($logoName, file_get_contents($logo));
                $company->logo = $logoName;
            }
            $company->company_name = $company_name;
            $company->scale = $scale;
            $company->website = $website;
            $company->number_phone = $number_phone;
            $company->address = $address;

            $company->save();
            return response()->json([
                "status" => 200,
                "success" => "Thành công"
            

            ]);
        }
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function confirmEmail(Request $request)
    {
        $companyEmail = $request->email;

        $company = Company::where("email", $companyEmail)->first();
        if ($company) {
            $verificationCode = strval(rand(100000, 999999));
            Mail::to($companyEmail)->send(new ForgotPassword($verificationCode));
        }

        return response()->json(
            $verificationCode
        );
    }



    public function getCompanyToken($token)
    {
        $company = Company::where("token", $token)->first();
        if (!$company) {
            return response()->json([
                "status" => 400,
                "message" => "Tài khoản không tồn tại"
            ]);
        }
        return response()->json(
            [
                "status" => 200,
                "company" => $company
            ]

        );
    }

    // admin user ---------------------------------------------------------
    public function getCandidateByCompany()
    {
        $users = DB::table('users')->get();
        return response()->json($users);
    }



    

    //  admin company------------------------------------

    public function getCompanyname()
    {
        $companies = DB::table('companies')->get();
        return response()->json($companies);
    }

    public function deleteCompany(Request $request)
    {
        $company = Company::find($request->id);

        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        $company->delete();

        return response()->json(['status' => 'ok', 'message' => 'Delete succeeded']);
    }



    public function getdatauser()
    {
        $jobs = Job::select('jobs.position', 'companies.company_name', 'applications.status', 'users.username')
            ->join('companies', 'jobs.company_id', '=', 'companies.id')
            ->join('applications', 'jobs.id', '=', 'applications.job_id')
            ->join('users', 'users.id', '=', 'applications.user_id')
            ->get();

        return response()->json($jobs);
    }

    // ---------------------------------------------------------------
    public function getJobByCompany($token)
    {

        $jobs = DB::table('jobs')
            ->select('jobs.id', 'jobs.cat_id', 'jobs.position', 'jobs.status', 'jobs.description', 'jobs.salary', 'jobs.type', 'jobs.created_at', 'jobs.close_day')
            ->join('companies', 'companies.id', '=', 'jobs.company_id')
            ->where('companies.token', $token)
            ->get();

        return response()->json($jobs);
    }

    public function CompanyChangePassword(Request $request)
    {
        $id = $request->id;
        $password = $request->password;
        $company = Company::where("id", $id)->first();
        if (!$company) {
            return response()->json(
                [
                    "status" => 400,
                    "message" => "Không thể đổi mật khẩu"
                ]

            );
        }
        $company->password = Hash::make($password);
        $company->save();
        return response()->json(
            [
                "status" => 200,
                "company" => $password
            ]
        );
    }


    public function comparePassword(Request $request)
    {
        $id = $request->id;
        $password = $request->password;
        $company = Company::where("id", $id)->first();
        if (!Hash::check($password, $company->password)) {
            return response()->json(
                [
                    "status" => 400,
                    "message" => "Mật khẩu sai"
                ]
            );
        }
        return response()->json(
            [
                "status" => 200,
            ]
        );
    }

    public function update(Request $request, $email)
    {
        $request->validate([
            "password" => "required|string|min:8"
        ]);
        $company = Company::where("email", $email)->first();

        if (!$company) {
            return response()->json(
                "Công ty không tồn tại"
            );
        }
        $company->password = bcrypt($request->password);
        $company->save();
        return response()->json(
            "Thành công"
        );
    }
}
