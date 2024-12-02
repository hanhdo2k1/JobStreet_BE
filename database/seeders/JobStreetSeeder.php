<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Company;
use App\Models\Categories;
use App\Models\Comment;
use App\Models\Job;
use App\Models\Contact;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

class JobStreetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fake = Faker::create();


        $categories1 = Categories::create([
            "name" => "Thực tập"
        ]);

        $categories2 = Categories::create([
            "name" => "Việc làm"
        ]);
       

        for ($i = 0; $i < 10; $i++) {

            // Tạo dữ liệu face với User
            $user = User::create([
                "username" => $fake->username,
                "avatar" => $fake->image,
                "email" => $fake->email,
                "password" => $fake->password,
                "token" =>$fake->lexify('??????'),
                "number_phone" => $fake->phoneNumber,
                "address" => $fake->address,
             
              
            ]);
            // Tạo dữ liệu face với Company
            $company = Company::create([
                "company_name" => $fake->name,
                "logo" => $fake->image,
                "scale" => rand(20,60),
                "description" => $fake->paragraph,
                "website" => $fake->url,
                "email" => $fake->email,
                "password" => $fake->password,
                "token" =>$fake->lexify('??????'),
                "number_phone" => $fake->phoneNumber,
                "address" => $fake->address,
               
                
            ]);
           // Tạo dữ liệu face với Job
            $salary = $fake->randomFloat(2, 1000, 5000);
            $startDate = date('Y-m-d');
            $close_day = $fake->date('Y-m-d', $startDate, '2000-12-31');
            $job = Job::create([
                "company_id" => $company->id,
                "cat_id" => ($i < 5) ? $categories1->id : $categories2->id,
                "position" => $fake->randomElement(['Java Dev', 'Laravel Dev', 'ReactJS Dev', 'FullStack', 'Design']),
                "salary" => $salary,
                "type" => $fake->randomElement(['Full-time', 'Part-time']),
                "description" => $fake->paragraph,
                "status" =>  $fake->randomElement(['Open', 'Closed']),
                "close_day" => $close_day,
            ]);

            // Tạo dữ liệu face với App~lication
            $application = Application::create([
                "user_id" => $user->id,
                "job_id" => $job->id,
                "cv" => $fake->slug . ".pdf",
                "status" => $fake->randomElement(['Pending', 'Approved', 'Rejected']),
            ]);

            $post = Post::create([
                "user_id" => $user->id,
                "title" => $fake->sentence,
                "body" => $fake->paragraph,
            ]);
 
            $comment = Comment::create([
                "user_id" => $user->id,
                "post_id" => $post->id,
                "content" => $fake->paragraph,
            ]);

            $contact = Contact::create([
                "user_id" => $user->id,
                "content" => $fake->paragraph,
            ]);
        }
    }
}
