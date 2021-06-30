<?php

declare(strict_types=1);

namespace App;

use App\Interfaces\Bookmarkable;
use App\Interfaces\LearningActivityInterface;
use App\Reflection\Models\ActivityReflection;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use App\Events\LearningActivityProducingCreated;

/**
 * App\LearningActivityActing.
 *
 * @property int $gla_id
 * @property int $wplp_id
 * @property Carbon $date
 * @property int $timeslot_id
 * @property string $situation
 * @property string $lessonslearned
 * @property string $support_wp
 * @property string $support_ed
 * @property int $res_material_id
 * @property string $res_material_detail ??
 * @property int $learninggoal_id
 * @property string $evidence_filename
 * @property string $evidence_disk_filename
 * @property string $evidence_mime
 * @property Timeslot $timeslot
 * @property ResourcePerson $resourcePerson
 * @property ResourceMaterial $resourceMaterial
 * @property LearningGoal $learningGoal
 * @property Collection|Competence[] $competence
 * @property \Illuminate\Database\Eloquent\Collection|\App\Evidence[] $evidence
 * @property \App\WorkplaceLearningPeriod $workplaceLearningPeriod
 * @property Chain $chain
 * @property Category $category
 * @property Feedback $feedback
 * @property int|null $res_person_id
 * @property string $learningactivity_name
 *
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereGlaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereLearninggoalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereResMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereResPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereTimeslotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereWplpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereEvidenceDiskFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereEvidenceFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereEvidenceMime($value)
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Genericlearningactivity whereChainId($value)

 * @mixin \Eloquent
 *
 * @property \App\Reflection\Models\ActivityReflection $reflection
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GenericLearningActivity query()
 */
class GenericLearningActivity extends Model implements LearningActivityInterface, Bookmarkable
{
    // Disable using created_at and updated_at columns
    public $timestamps = false;

    // Override the table used for the User Model
    protected $table = 'genericlearningactivity';

    // Override the primary key column
    protected $primaryKey = 'gla_id';

    //protected $dates = ['date'];

    // Default
    /**
     * @var string[]
     */
    protected $fillable = [
        'gla_id',
        'wplp_id',
        'timeslot_id',
        'res_person_id',
        'res_material_id',
        'learninggoal_id',
        'category_id',
        'chains_id',
        'learningactivity_name'


    ];

    public function learningGoal(): BelongsTo
    {
        return $this->belongsTo(LearningGoal::class, 'learninggoal_id', 'learninggoal_id');
    }

    public function competence(): BelongsToMany
    {
        return $this->belongsToMany(Competence::class, 'activityforcompetence', 'learningactivity_id', 'competence_id');
    }

    public function timeslot(): BelongsTo
    {
        return $this->belongsTo(Timeslot::class, 'timeslot_id', 'timeslot_id');
    }

    public function resourcePerson(): BelongsTo
    {
        return $this->belongsTo(ResourcePerson::class, 'res_person_id', 'rp_id');
    }

    public function resourceMaterial(): BelongsTo
    {
        return $this->belongsTo(ResourceMaterial::class, 'res_material_id', 'rm_id');
    }

    public function workplaceLearningPeriod(): BelongsTo
    {
        return $this->belongsTo(WorkplaceLearningPeriod::class, 'wplp_id', 'wplp_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'genericlearningactivity_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class, 'gla_id', 'gla_id');

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





    // Relations for query builder
    public function getRelationships(): array
    {
        return [
            'learningGoal',
            'competence',
            'timeslot',
            'resourcePerson',
            'resourceMaterial',
            'workplaceLearningPeriod',
            'feedback',
            'category',
            'column'
        ];
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class, 'genericlearningactivity_id', 'gla_id');
    }

    /**
     * @throws RuntimeException
     */
    public function reflection(): HasOne
    {
        return $this->hasOne(ActivityReflection::class, 'genericlearningactivity_id', 'gla_id');
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

    public function bookmarkCheck($gla_id = 0): array
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
