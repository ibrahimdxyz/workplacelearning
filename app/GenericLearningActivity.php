<?php

namespace App;

use App\Interfaces\Bookmarkable;
use App\Interfaces\LearningActivityInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * App\GenericLearningActivity.
 *
 *
 * @property int $gla_id
 * @property int $wplp_id
 * @property Carbon $date
 * @property int $res_person_id
 * @property int $res_material_id
 * @property int $category_id
 * @property int $difficulty_id
 * @property int $status_id
 * @property int $timeslot_id
 * @property int $learninggoal_id
 * @property int $chain_id
 * @property string $support_wp
 * @property string $support_ed
 * @property Feedback $feedback
 * @property Timeslot $timeslot
 * @property ResourcePerson $resourcePerson
 * @property ResourceMaterial $resourceMaterial
 * @property LearningGoal $learningGoal
 * @property \App\WorkplaceLearningPeriod $workplaceLearningPeriod
 * @property string learningactivity_name
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereGlaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereResMaterialDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereResMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereResPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereSituation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereTimeslotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereWplpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereLearningActivityName($value)
 *  @mixin \Eloquent
 *
 * @property \App\Reflection\Models\ActivityReflection $reflection
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity query()

 *
 * */
class GenericLearningActivity extends Model implements LearningActivityInterface, Bookmarkable
{


    // Disable using created_at and updated_at columns
    public $timestamps = false;

    // Override the table used for the User Model
    protected $table = 'genericlearningactivity';

    // Override the primary key column
    protected $primaryKey = 'gla_id';


    protected $fillable = ['gla_id','wplp_id', 'date', 'workplaceLearningPeriod', 'feedback', 'resourcePerson', 'resourceMaterial', 'category', 'difficulty','status', 'learningactivity_name', ];


    protected $appends = ['duration', 'description', 'res_material_detail', 'extra_feedback', 'situation', 'lessons_learned', 'support_wp', 'support_ed'];


    protected $dates = ['date'];


    // Custom attributes

    //// accessors

    public function getDurationAttribute($value)
    {
        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        return $value;
    }

    public function getResMaterialDetailAttribute($value)
    {
        return $value;
    }

    public function getExtraFeedbackAttribute($value)
    {
        return $value;
    }


    public function getSituationAttribute($value)
    {
        return $value;
    }


    public function getLessonsLearnedAttribute($value)
    {
        return $value;
    }


    public function getSupportWpAttribute($value)
    {
        return $value;
    }


    public function getSupportEdAttribute($value)
    {
        return $value;
    }



    ////  mutators

    public function setDurationAttribute($value)
    {
        return $this->attributes['duration'] = $value;
    }

    public function setDescriptionAttribute($value)
    {
        return $this->attributes['description'] = $value;
    }



    public function setResMaterialDetailAttribute($value)
    {
        return $this->attributes['res_material_detail'] = $value;
    }

    public function setExtraFeedbackAttribute($value)
    {
        return $this->attributes['extra_feedback'] = $value;
    }


    public function setSituationAttribute($value)
    {
        return $this->attributes['situation'] = $value;
    }


    public function setLessonsLearnedAttribute($value)
    {
        return $this->attributes['lessons_learned'] = $value;
    }


    public function setSupportWpAttribute($value)
    {
        return $this->attributes['support_wp'] = $value;
    }


    public function setSupportEdAttribute($value)
    {
        return $this->attributes['support_ed'] = $value;
    }



    // Default attributes

    public function workplaceLearningPeriod(): BelongsTo
    {
        return $this->belongsTo(WorkplaceLearningPeriod::class, 'wplp_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'learningactivity_id');
    }

    public function resourcePerson(): BelongsTo
    {
        return $this->belongsTo(ResourcePerson::class, 'res_person_id');
    }

    public function resourceMaterial(): BelongsTo
    {
        return $this->belongsTo(ResourceMaterial::class, 'res_material_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(Difficulty::class, 'difficulty_id', 'difficulty_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'status_id');
    }

    // Relations for query builder
    public function getRelationships(): array
    {
        return [
            'workplaceLearningPeriod',
            'feedback',
            'resourcePerson',
            'resourceMaterial',
            'category',
            'difficulty',
            'status',
        ];
    }



    // Note: DND, object comparison
    public function __toString()
    {
        return $this->gla_id.'';
    }

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
    }

    public function isWithinWplpDates(): bool
    {
        return $this->date->greaterThanOrEqualTo($this->workplaceLearningPeriod->startdate)
            && $this->date->lessThanOrEqualTo($this->workplaceLearningPeriod->enddate);
    }


    public function getDescription(): string
    {
        return $this->situation;
    }

    public function getDate(): DateTime
    {
        return $this->date->toDateTime();
    }




    public function bookmark(): SavedLearningItem
    {
        $savedLearningItem = new SavedLearningItem();
        $savedLearningItem->category = SavedLearningItem::CATEGORY_GLA;
        $savedLearningItem->item()->associate($this->gla_id);
        $savedLearningItem->student()->associate($this->workplaceLearningPeriod->student);
        $savedLearningItem->created_at = new \DateTimeImmutable();
        $savedLearningItem->updated_at = new \DateTimeImmutable();

        return $savedLearningItem;
    }


    public function bookmarkCheck($gla_id= 0): array
    {
        $bookmarkCheck = array();
        $student_nr = $this->bookmark()->student_id;
        $bookmarked = DB::table('saved_learning_items')->where([
            ['item_id', '=', $gla_id],
            ['student_id', '=', $student_nr],
        ])->get();
        if(count($bookmarked) > 0) {
            $bookmarkCheck[] = 1;
        }
        return $bookmarkCheck;
    }

}
