<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Company extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $fillable=[
        "company_name",
        "logo",
        "scale",
        "description",
        "website",
        "email",
        "password",
        "address",
        "number_phone",
        "token"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function job()
    {
        return $this->hasMany(Job::class);
    }

    public function getApplicationsByCompany(Request $request)
    {
        $companyId = $request->input('company_id');

        // Lấy danh sách các công việc của công ty
        $jobs = Job::where('company_id', $companyId)->get();

        // Lấy danh sách ứng dụng từ các công việc
        $applications = [];

        foreach ($jobs as $job) {
            $jobApplications = $job->applications()->get();
            $applications = array_merge($applications, $jobApplications->toArray());
        }

        return response()->json(['applications' => $applications], 200);
    }
}
