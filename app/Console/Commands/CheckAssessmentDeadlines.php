<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assessment;
use App\Models\Assessor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckAssessmentDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessment:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check assessment deadlines and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking assessment deadlines...');

        // Get assessments with deadlines
        $assessments = Assessment::with(['calon_mahasiswa', 'assessor1', 'assessor2', 'assessor3'])
            ->whereNotNull('deadline')
            ->get();

        $overdueCount = 0;
        $approachingCount = 0;

        foreach ($assessments as $assessment) {
            $deadline = Carbon::parse($assessment->deadline);
            $now = Carbon::now();

            // Check if deadline has passed
            if ($deadline->isPast()) {
                $overdueCount++;
                $this->warn("OVERDUE: Assessment for {$assessment->calon_mahasiswa->nama} was due on {$deadline->format('d/m/Y H:i')}");
                
                // Log overdue assessment
                Log::warning("Assessment deadline overdue", [
                    'assessment_id' => $assessment->id,
                    'calon_mahasiswa_id' => $assessment->calon_mahasiswa_id,
                    'calon_mahasiswa_nama' => $assessment->calon_mahasiswa->nama,
                    'deadline' => $deadline->format('Y-m-d H:i:s'),
                    'assessors' => [
                        'assessor1' => $assessment->assessor1->nama ?? 'Not assigned',
                        'assessor2' => $assessment->assessor2->nama ?? 'Not assigned',
                        'assessor3' => $assessment->assessor3->nama ?? 'Not assigned',
                    ]
                ]);
            }
            // Check if deadline is approaching (within 3 days)
            elseif ($deadline->diffInDays($now) <= 3 && $deadline->isFuture()) {
                $approachingCount++;
                $this->info("APPROACHING: Assessment for {$assessment->calon_mahasiswa->nama} is due on {$deadline->format('d/m/Y H:i')} ({$deadline->diffForHumans()})");
                
                // Log approaching deadline
                Log::info("Assessment deadline approaching", [
                    'assessment_id' => $assessment->id,
                    'calon_mahasiswa_id' => $assessment->calon_mahasiswa_id,
                    'calon_mahasiswa_nama' => $assessment->calon_mahasiswa->nama,
                    'deadline' => $deadline->format('Y-m-d H:i:s'),
                    'days_remaining' => $deadline->diffInDays($now),
                    'assessors' => [
                        'assessor1' => $assessment->assessor1->nama ?? 'Not assigned',
                        'assessor2' => $assessment->assessor2->nama ?? 'Not assigned',
                        'assessor3' => $assessment->assessor3->nama ?? 'Not assigned',
                    ]
                ]);
            }
        }

        $this->info("Found {$overdueCount} overdue assessments and {$approachingCount} approaching deadlines.");
        
        if ($overdueCount > 0 || $approachingCount > 0) {
            $this->warn("Total assessments requiring attention: " . ($overdueCount + $approachingCount));
        } else {
            $this->info("All assessments are within their deadlines.");
        }

        return 0;
    }
}
