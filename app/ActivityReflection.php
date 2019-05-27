<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

/**
 * App\ActivityReflection
 *
 * @property int $id
 * @property int $learning_activity_id
 * @property string $learning_activity_type
 * @property string $reflection_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ActivityReflectionField[] $fields
 * @property-read \App\LearningActivityActing $learningActivity
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityReflection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityReflection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityReflection query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityReflection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityReflection whereLearningActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityReflection whereLearningActivityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityReflection whereReflectionType($value)
 * @mixin \Eloquent
 */
class ActivityReflection extends Model
{
    public $timestamps = false;

    public const LEARNING_ACTIVITY_ACTING = 'acting';
    public const LEARNING_ACTIVITY_PRODUCING = 'producing';
    public const LEARNING_ACTIVITY_TYPE = [LearningActivityActing::class => self::LEARNING_ACTIVITY_ACTING, LearningActivityProducing::class => self::LEARNING_ACTIVITY_PRODUCING];

    public const TYPES = ['STARR', 'KORTHAGEN', 'ABCD', 'PDCA', 'CUSTOM'];
    public const READABLE_TYPES = ['STARR' => 'STARR', 'KORTHAGEN' => 'Korthagen', 'ABCD' => 'ABCD', 'PDCA' => 'PDCA', 'CUSTOM' => 'Custom'];

    public function fields(): HasMany
    {
        return $this->hasMany(ActivityReflectionField::class);
    }

    /**
     * @throws RuntimeException
     */
    public function learningActivity(): BelongsTo
    {
        if ($this->learning_activity_type === self::LEARNING_ACTIVITY_ACTING) {
            return $this->belongsTo(LearningActivityActing::class, 'learning_activity_id', 'laa_id');
        }

        if ($this->learning_activity_type === self::LEARNING_ACTIVITY_PRODUCING) {
            return $this->belongsTo(LearningActivityProducing::class, 'learning_activity_id', 'lap_id');
        }

        throw new RuntimeException("ActivityReflection with type {$this->type} cannot be related to a learning activity");
    }
}
