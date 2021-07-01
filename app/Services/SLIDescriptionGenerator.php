<?php
declare(strict_types=1);

namespace App\Services;


use App\Interfaces\LearningActivityInterface;
use App\Repository\Eloquent\GenericLearningActivityRepository;
use App\Repository\Eloquent\LearningActivityActingRepository;
use App\Repository\Eloquent\LearningActivityProducingRepository;
use App\Repository\Eloquent\GenericLearningActivityRepositoryRepository;
use App\Repository\Eloquent\TipRepository;
use App\SavedLearningItem;
use App\Tips\Services\TipEvaluator;

class SLIDescriptionGenerator
{
    private $tipEvaluator;

    private $tipRepository;

    private $genericRepository;

    public function __construct(TipEvaluator $evaluator, TipRepository $tipRepository,  GenericLearningActivityRepository $genericRepository)
    {
        $this->tipEvaluator = $evaluator;
        $this->tipRepository = $tipRepository;
        $this->genericRepository = $genericRepository;

    }

    public function getDescriptionForSLI(SavedLearningItem $savedLearningItem): string
    {
        if($savedLearningItem->category === SavedLearningItem::CATEGORY_TIP) {
            $tip = $this->tipRepository->get($savedLearningItem->item_id);
            return $this->tipEvaluator->evaluateForChosenStudent($tip, $savedLearningItem->student)->getTipText();
        }

        return $this->getLearningActivity($savedLearningItem->category, $savedLearningItem->item_id)->getDescription();
    }

    private function getLearningActivity(string $type, int $id): LearningActivityInterface
    {
        if($type === SavedLearningItem::CATEGORY_GLA) {
            return $this->genericRepository->get($id);

        }

        return $this->genericRepository->get($id);

    }
}
