<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'user_id',
        'overall_satisfaction',
        'process_rating',
        'support_rating',
        'timeliness_rating',
        'comments',
        'suggestions',
        'would_recommend',
        'completed_at',
    ];

    protected $casts = [
        'overall_satisfaction' => 'integer',
        'process_rating' => 'integer',
        'support_rating' => 'integer',
        'timeliness_rating' => 'integer',
        'would_recommend' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the return request associated with the survey
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    /**
     * Get the user who completed the survey
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a survey for a completed return
     */
    public static function generateSurvey(ReturnRequest $returnRequest): self
    {
        $survey = self::create([
            'return_request_id' => $returnRequest->id,
            'user_id' => $returnRequest->user_id,
        ]);

        // Create a notification to ask the user to complete the survey
        ReturnNotification::createStatusNotification($returnRequest, ReturnNotification::TYPE_SURVEY)
            ->send();

        return $survey;
    }

    /**
     * Calculate average ratings for reporting
     */
    public static function getAverageRatings(string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonths(3)->format('Y-m-d');
        $endDate = $endDate ?? now()->format('Y-m-d');

        $surveys = self::whereBetween('completed_at', [$startDate, $endDate])
            ->whereNotNull('completed_at')
            ->get();

        $count = $surveys->count();

        if ($count === 0) {
            return [
                'average_overall_satisfaction' => 0,
                'average_process_rating' => 0,
                'average_support_rating' => 0,
                'average_timeliness_rating' => 0,
                'recommendation_percentage' => 0,
                'survey_count' => 0,
            ];
        }

        return [
            'average_overall_satisfaction' => $surveys->avg('overall_satisfaction'),
            'average_process_rating' => $surveys->avg('process_rating'),
            'average_support_rating' => $surveys->avg('support_rating'),
            'average_timeliness_rating' => $surveys->avg('timeliness_rating'),
            'recommendation_percentage' => ($surveys->where('would_recommend', true)->count() / $count) * 100,
            'survey_count' => $count,
        ];
    }
}
