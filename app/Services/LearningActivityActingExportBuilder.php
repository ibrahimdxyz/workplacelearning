<?php

declare(strict_types=1);

namespace App\Services;

use App\Competence;
use App\Evidence;
use App\GenericLearningActivity;
//use App\LearningActivityActing;
use Carbon\Carbon;
use Illuminate\Translation\Translator;

class LearningActivityActingExportBuilder
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getJson(array $learningActivities, ?int $limit): string
    {
        $jsonArray = [];

        $collection = collect($learningActivities);
        if ($limit !== null) {
            $collection = $collection->take($limit);
        }

        $collection->each(function (GenericLearningActivity $genericLearningActivity) use (&$jsonArray): void {
            $jsonArray[] = [
                'id'                      => $genericLearningActivity->gla_id,
                'isSaved'                 => $genericLearningActivity->bookmarkCheck($genericLearningActivity->gla_id),
//                'date'                    => $activity->date->format('d-m-Y'),
                //FORMAT
                'date'                    => Carbon::parse($genericLearningActivity->column()->where("name", "date")->first()->column_data()->data_as_string)->format('d-m-Y'),
//                'situation'               => $activity->situation,
                'situation'          => $genericLearningActivity->column()->where("name", "situation")->first()->column_data()->data_as_string,
//                'timeslot'                => $activity->timeslot->localizedLabel(),
                'timeslot'                => $genericLearningActivity->timeslot->localizedLabel(),
//                'resourcePerson'          => $activity->resourcePerson->localizedLabel(),
                'resourcePerson'          => $genericLearningActivity->resourcePerson->localizedLabel(),
//                'resourceMaterial'        => __($activity->resourceMaterial ? $activity->resourceMaterial->rm_label : 'activity.none'),
                'resourceMaterial'        => __($genericLearningActivity->resourceMaterial ? $genericLearningActivity->resourceMaterial->rm_label : 'activity.none'),
//                'learningGoal'            => __($activity->learningGoal->learninggoal_label),
                'learningGoal'            => __($genericLearningActivity->learningGoal->learninggoal_label),

//                'competence'              => $activity->competence->map(function (Competence $competence) {
                'competence'              => $genericLearningActivity->competence->map(function (Competence $competence) {


                    return $competence->localizedLabel();
                })->all(),
//                'learningGoalDescription' => $activity->learningGoal->description,
                'learningGoalDescription' => $genericLearningActivity->learningGoal->description,
//                'lessonsLearned'          => $activity->lessonslearned,
                'lessonsLearned'          => $genericLearningActivity->column()->where("name", "lessonslearned")->first()->column_data()->data_as_string,
//                'supportWp'               => $activity->support_wp ?? '',
                //STAAT ??
                'supportWp'               => $genericLearningActivity->column()->where("name", "support_wp")->first()->column_data()->data_as_string ?? '',
//                'supportEd'               => $activity->support_ed ?? '',
                //STAAT ??
                'supportEd'          => $genericLearningActivity->column()->where("name", "support_ed")->first()->column_data()->data_as_string,
//                'url'                     => route('process-acting-edit', [$activity->laa_id]),
                'url'                     => route('process-acting-edit', [$genericLearningActivity->gla_id]),

                'evidence'                => $genericLearningActivity->evidence->map(function (Evidence $evidence) {
                    return [
                        'name'          => $evidence->filename,
                        'url'           => route('evidence-download',
                            ['evidence' => $evidence, 'diskFileName' => $evidence->disk_filename]),
                    ];
                })->all(),
                'reflection'              => (static function () use ($genericLearningActivity) {
                    if ($genericLearningActivity->reflection === null) {
                        return ['url' => null, 'id' => null];
                    }

                    return [
                        'url' => route('reflection-download', [$genericLearningActivity->reflection]),
                        'id'  => $genericLearningActivity->reflection->id,
                    ];
                })(),
            ];
        });

        return json_encode($jsonArray);
    }
//??
    public function getFieldLanguageMapping(): array
    {
        $mapping = [];
        collect([
            'date',
            'situation',
            'timeslot',
            'resourcePerson',
            'resourceMaterial',
            'lessonsLearned',
            'learningGoal',
            'learningGoalDescription',
            'supportWp',
            'supportEd',
            'competence',
            'evidence',
            'reflection',
        ])->each(function ($field) use (&$mapping): void {
            $mapping[$field] = $this->translator->get('process_export.'.$field);
        });

        return $mapping;
    }
}
