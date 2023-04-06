<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;   
use Illuminate\Support\Carbon; 
class UpdateUserIsNewStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // $subDays = now()->subDays(2);
        $newUsers = User::where('created_at', '<',Carbon::now()->subDays(2))->get();
        // $newUsers = User::where('created_at', '<',Carbon::now()->subMinutes(5))->get();
        // $newUsers = User::where('created_at', '>=', $subDays)->get();
        
        foreach ($newUsers as $user) {
            $user->is_new = 0;
            $user->save();
        }
    }
    
    
    
}
