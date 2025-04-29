<?php

namespace App\Jobs;

use App\Models\UserDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessUserJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $userId;

    /**
     * Create a new job instance.
     *
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }
    /**
     * Get Unique ID for the job
     */
    public function uniqueId(): string {
        return $this->userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            error_log("hey user_id". $this->userId);
            $user = UserDetail::where('user_id', $this->userId)->first();

            if (!$user) {
                Log::error('User not found.', ['user_id' => $this->userId]);
                return;
            }

            $user->delete();
            Log::info('User deleted successfully.', ['user_id' => $this->userId]);
        } catch (\Exception $e) {
            Log::critical('Error handling user deletion.', [
                'user_id' => $this->userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
