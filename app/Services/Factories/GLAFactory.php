<?php


namespace App\Services\Factories;


use App\Reflection\Services\Factories\ActivityReflectionFactory;
use App\Repository\Eloquent\GenericLearningActivityRepository;
use App\Repository\Eloquent\LearningActivityActingRepository;
use App\Services\CurrentUserResolver;

class GLAFactory
{
    /**
     * @var TimeslotFactory
     */
    private $timeslotFactory;
    /**
     * @var ResourcePersonFactory
     */
    private $resourcePersonFactory;
    /**
     * @var ResourceMaterialFactory
     */
    private $resourceMaterialFactory;
    /**
     * @var CurrentUserResolver
     */
    private $currentUserResolver;
    /**
     * @var GenericLearningActivityRepository
     */
    private $genericLearningActivityRepository;

    /**
     * @var ActivityReflectionFactory
     */
    private $activityReflectionFactory;



    public function __construct(
        GenericLearningActivityRepository $genericLearningActivityRepository,
        TimeslotFactory $timeslotFactory,
        ResourcePersonFactory $resourcePersonFactory,
        ResourceMaterialFactory $resourceMaterialFactory,
        CurrentUserResolver $currentUserResolver,
        ActivityReflectionFactory $activityReflectionFactory
    ) {
        $this->timeslotFactory = $timeslotFactory;
        $this->resourcePersonFactory = $resourcePersonFactory;
        $this->resourceMaterialFactory = $resourceMaterialFactory;
        $this->currentUserResolver = $currentUserResolver;
        $this->activityReflectionFactory = $activityReflectionFactory;
        $this->genericLearningActivityRepository = $genericLearningActivityRepository;

        $this->chainFactory = $chainFactory;
        $this->categoryFactory = $categoryFactory;

    }

}