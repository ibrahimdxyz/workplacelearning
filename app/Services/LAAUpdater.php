<?php

declare(strict_types=1);

namespace App\Services;

use App\Column;
use App\column_data;
use App\fieldtype;
use App\GenericLearningActivity;
use App\LearningActivityActing;
use App\Reflection\Services\Factories\ActivityReflectionFactory;
use App\Reflection\Services\Updaters\ActivityReflectionUpdater;
//use App\Repository\Eloquent\LearningActivityActingRepository;
use App\Repository\Eloquent\GenericLearningActivityRepository;
use Carbon\Carbon;

class LAAUpdater
{
    /**
     * @var ActivityReflectionUpdater
     */
    private $activityReflectionUpdater;
    /**
     * @var ActivityReflectionFactory
     */
    private $activityReflectionFactory;
//    /**
//     * @var LearningActivityActingRepository
//     */
//    private $learningActivityActingRepository;
    /**
     * @var GenericLearningActivityRepository
     */
    private $genericLearningActivityRepository;


    public function __construct(
        ActivityReflectionUpdater $activityReflectionUpdater,
        ActivityReflectionFactory $activityReflectionFactory,
        GenericLearningActivityRepository $genericLearningActivityRepository

    ) {
        $this->activityReflectionUpdater = $activityReflectionUpdater;
        $this->activityReflectionFactory = $activityReflectionFactory;

        $this->genericLearningActivityRepository = $genericLearningActivityRepository;
    }

    public function update(GenericLearningActivity $genericLearningActivity, array $data): bool
    {
        //choosing fieldTypes from database
        $radiobutton = Fieldtype::where("fieldtype","radiobutton")->first();
        $text = Fieldtype::where("fieldtype","text")->first();
        $datePicker = Fieldtype::where("fieldtype","date")->first();
        $button = Fieldtype::where("fieldtype","date")->first();

        if (isset($data['datum'])) {
            $column = $this->createColumn("date", null, Carbon::parse($data['datum'])->format('Y-m-d'), $datePicker, "date");
            $genericLearningActivity->column()->associate($column);
        }
        if (isset($data['description'])) {
            $column = $this->createColumn("situation", null, $data['description'], $text, "string");
            $genericLearningActivity->column()->associate($column);
        }
        if (isset($data['learned'])) {
            $column = $this->createColumn("lessonslearned", null, $data['learned'], $text, "string");
            $genericLearningActivity->column()->associate($column);
        }
        if (isset($data['support_wp'])) {
            $column = $this->createColumn("support_wp", null, $data['support_wp'], $text, "string");
            $genericLearningActivity->column()->associate($column);
        }
        if (isset($data['support_ed'])) {
            $column = $this->createColumn("support_ed", null, $data['support_ed'], $text, "string");
            $genericLearningActivity->column()->associate($column);
        }

        $genericLearningActivity->timeslot()->associate($data['timeslot']);
        $genericLearningActivity->resourcePerson()->associate($data['res_person']);
        $genericLearningActivity->learningGoal()->associate($data['learning_goal']);

        $genericLearningActivity->competence()->sync($data['competence']);


        if ($data['res_material'] === 'none') {
            $genericLearningActivity->resourceMaterial()->dissociate();
        } else {
            $genericLearningActivity->resourceMaterial()->associate($data['res_material']);
        }

        $genericLearningActivity->res_material_detail = $data['res_material_detail'];
        if (isset($data['res_material_detail'])) {
            $columnOptions = "['persoon','internet','boek', 'alleen']";
            $column = $this->createColumn("res_material_detail",$columnOptions, $data['res_material_detail'], $button, "string");
            $genericLearningActivity->column()->associate($column);
        }
        // If a reflection already exists the user is trying to update its contents
        if (isset($data['reflection'])) {
            if ($genericLearningActivity->reflection) {
                $this->activityReflectionUpdater->update($genericLearningActivity->reflection, $data['reflection']);
            } else {
                // If no reflection exists (manually removed or creating afterwards) we create one instead
                $reflection = $this->activityReflectionFactory->create($data['reflection'], $genericLearningActivity);
            }
        }

        return $this->genericLearningActivityRepository->save($genericLearningActivity);
    }
    private function createColumn($name, $columnOptions, $data, $fieldType, $dataType) {
        $column = new Column;
        $column->name = $name;
        $column->columnOptions = $columnOptions;
        $column->fieldType()->associate($fieldType);
        $column->save();

        $columnData = new column_data();
        $columnData->column()->associate($column);
        $columnData->data_as_string = $data;
        $columnData->dataType = $dataType;
        $columnData->save();

        return $column;
    }
}
