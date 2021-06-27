<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GenericLearningActivity extends Model
{


    protected $fillable = ['workplaceLearningPeriod', 'feedback', 'resourcePerson', 'resourceMaterial', 'category', 'difficulty','status', ];


    protected $appends = ['duration', 'description', 'res_material_detail', 'extra_feedback', 'situation', 'lessons_learned', 'support_wp', 'support_ed'];


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];


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

}
