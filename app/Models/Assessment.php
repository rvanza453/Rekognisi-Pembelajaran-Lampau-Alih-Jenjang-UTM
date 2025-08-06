<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Assessment extends Model
{
    use HasFactory;

    protected $table = 'assessment';
    protected $fillable = [
        'calon_mahasiswa_id',
        'jurusan_id',
        'assessor_id_1',
        'assessor_id_2',
        'assessor_id_3',
        'deadline',
        'rpl_status',
        'self_assessment_submitted_at',
    ];
    
    protected $casts = [
        'deadline' => 'datetime',
        'self_assessment_submitted_at' => 'datetime',
    ];
    
    public function calon_mahasiswa()
    {
        return $this->belongsTo(Calon_mahasiswa::class, 'calon_mahasiswa_id');
    }
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
    Public function assessor1()
    {
        return $this->belongsTo(Assessor::class, 'assessor_id_1');
    }
    public function assessor2()
    {
        return $this->belongsTo(Assessor::class, 'assessor_id_2');
    }
    public function assessor3()
    {
        return $this->belongsTo(Assessor::class, 'assessor_id_3');
    }
    
    // Helper methods for deadline statistics
    public static function getDeadlineStatistics()
    {
        $now = Carbon::now();

        $query = self::whereNotNull('deadline')
            ->whereIn('rpl_status', ['self-assessment', 'penilaian assessor']);

        $totalAssessments = (clone $query)->count();
        $overdueAssessments = (clone $query)->where('deadline', '<', $now)->count();
        $approachingAssessments = (clone $query)
            ->where('deadline', '>', $now)
            ->where('deadline', '<=', $now->copy()->addDays(3))
            ->count();
        $activeAssessments = (clone $query)
            ->where('deadline', '>', $now->copy()->addDays(3))
            ->count();

        return [
            'total' => $totalAssessments,
            'overdue' => $overdueAssessments,
            'approaching' => $approachingAssessments,
            'active' => $activeAssessments,
        ];
    }
    
    public static function getAssessorDeadlineStatistics($assessorId)
    {
        $now = Carbon::now();

        $query = self::whereNotNull('deadline')
            ->whereIn('rpl_status', ['self-assessment', 'penilaian assessor'])
            ->where(function($query) use ($assessorId) {
                $query->where('assessor_id_1', $assessorId)
                      ->orWhere('assessor_id_2', $assessorId)
                      ->orWhere('assessor_id_3', $assessorId);
            });

        $totalAssessments = (clone $query)->count();
        $overdueAssessments = (clone $query)->where('deadline', '<', $now)->count();
        $approachingAssessments = (clone $query)
            ->where('deadline', '>', $now)
            ->where('deadline', '<=', $now->copy()->addDays(3))
            ->count();

        return [
            'total' => $totalAssessments,
            'overdue' => $overdueAssessments,
            'approaching' => $approachingAssessments,
        ];
    }
}