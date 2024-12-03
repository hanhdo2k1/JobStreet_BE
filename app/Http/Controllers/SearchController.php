<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function getALlJobs()
    {
        $data['jobs'] = Job::select('position', 'type','salary','id','description','close_day', 'company_id')
        ->with('company:id,company_name,address,logo',)
        ->get();
    }
    // public function search(Request $request)
    // {
    //     $positions = Job::whereIn('position', ['Design', 'Java Dev', 'FullStack', 'ReactJS Dev', 'Laravel Dev'])->get();
    //     $types = Job::whereIn('type', ['Full-time', 'Part-time'])->get();
    
    //     return response()->json(['position'=>$positions,'type'=> $types]);
    // }
}
