<?php

declare(strict_types=1);

namespace App\Services;

use App\GenericLearningActivity;
use App\SavedLearningItem;
use Carbon\Carbon;
use Illuminate\Translation\Translator;
class LearningActivityProducingExportBuilder
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getJson(array $learningActivities, ?int $limit = null): string
    {
        $jsonArray = [];

        $collection = collect($learningActivities);
        if ($limit !== null) {
            $collection = $collection->take($limit);
        }

        $collection->each(function (GenericLearningActivity $genericLearningActivity) use (&$jsonArray): void {
            $jsonArray[] = [
                'id'              => $genericLearningActivity->gla_id,
                'isSaved'         => $genericLearningActivity->bookmarkCheck($genericLearningActivity->gla_id),
                //'date'            => $genericLearningActivity->date->format('d-m-Y'),
                'date'            => Carbon::parse($genericLearningActivity->column()->where("name", "date")->first()->column_data()->data_as_string)->format('d-m-Y'),
                //'duration'        => $this->formatDuration($genericLearningActivity->duration),
                'duration'        => $this->formatDuration($genericLearningActivity->column()->where("name", "duration")->first()->column_data()->data_as_string),
                //'hours'           => $genericLearningActivity->duration,
                'hours'           => $genericLearningActivity->column()->where("name", "duration")->first()->column_data()->data_as_string,
                //'description'     => $genericLearningActivity->description,
                'description'     => $genericLearningActivity->column()->where("name", "situation")->first()->column_data()->data_as_string,
                'resourceDetail'  => $this->formatResourceDetail($genericLearningActivity),
                'category'        => $genericLearningActivity->category->localizedLabel(),
                //'difficulty'      => $this->translator->get('general.'.strtolower($genericLearningActivity->difficulty->difficulty_label)),
                'difficulty'      => $this->translator->get('general.'.strtolower($genericLearningActivity->column()->where("name", "difficulty")->first()->column_data()->data_as_string)),
                //'difficultyValue' => $genericLearningActivity->difficulty->difficulty_id,
                //'status'          => $this->translator->get('general.'.strtolower($genericLearningActivity->where("name", "status")->first()->column_data()->data_as_string)),
                'status'      => $this->translator->get('general.'.strtolower($genericLearningActivity->column()->where("name", "status")->first()->column_data()->data_as_string)),
                'url'             => route('process-producing-edit', [$genericLearningActivity->gla_id]),
                'chain'           => $this->formatChain($genericLearningActivity),
                'feedback'              => (static function () use ($genericLearningActivity) {
                    if ($genericLearningActivity->feedback === null) {
                        return null;
                    }

                    return [
                        'fb_id'                 => $genericLearningActivity->feedback['fb_id'] ?? null,
                        'notfinished'           => $genericLearningActivity->feedback['notfinished'] ?? null,
                        'initiative'            => $genericLearningActivity->feedback['initiative'] ?? null,
                        'progress_satisfied'    => $genericLearningActivity->feedback['progress_satisfied'] ?? null,
                        'support_requested'     => $genericLearningActivity->feedback['support_requested'] ?? null,
                        'supported_provided_wp' => $genericLearningActivity->feedback['supported_provided_wp'] ?? null,
                        'nextstep_self'         => $genericLearningActivity->feedback['nextstep_self'] ?? null,
                        'support_needed_wp'     => $genericLearningActivity->feedback['support_needed_wp'] ?? null,
                        'support_needed_ed'     => $genericLearningActivity->feedback['support_needed_ed'] ?? null,
                    ];
                })(),
            ];
        });

        return json_encode($jsonArray);
    }

    private function formatDuration(float $duration): string
    {
        switch ($duration) {
            case 0.25:
                return '15 min';
            case 0.5:
                return '30 min';
            case 0.75:
                return '45 min';
            case $duration < 1:
                return round($duration * 60).' min';
            default:
                return $duration.' '.$this->translator->get('general.hour');
        }
    }

    private function formatResourceDetail(GenericLearningActivity $genericLearningActivity): string
    {
        if ($genericLearningActivity->resourceMaterial) {
//            return $this->translator->get($genericLearningActivity->resourceMaterial->rm_label).': '.$genericLearningActivity->res_material_detail;
            return $this->translator->get($genericLearningActivity->resourceMaterial->rm_label).': '.$genericLearningActivity->column()->where("name", "res_material_detail")->first()->column_data()->data_as_string;

        }

        if ($genericLearningActivity->resourcePerson) {
            return $this->translator->get('activity.producing.person').': '.__($genericLearningActivity->resourcePerson->localizedLabel());
        }

        return $this->translator->get('activity.alone');
    }

    private function formatChain(GenericLearningActivity $genericLearningActivity): string
    {

        if (!$genericLearningActivity->chain) {
            return '-';
        }
        $hours = strtolower($this->translator->get('activity.hours'));

        return $genericLearningActivity->chain->name." ({$genericLearningActivity->chain->hours()} {$hours})";
    }

    public function getFieldLanguageMapping(): array
    {
        $mapping = [];
        collect([
            'id',
            'date',
            'duration',
            'hours',
            'description',
            'resourceDetail',
            'category',
            'difficulty',
            'status',
            'chain',
        ])->each(function ($field) use (&$mapping): void {
            $mapping[$field] = $this->translator->get('process_export.'.$field);
        });
        $mapping['feedback'] = 'feedback';

        return $mapping;
    }
}
