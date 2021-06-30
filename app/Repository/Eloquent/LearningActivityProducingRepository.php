<?php
//
//declare(strict_types=1);
//
//namespace App\Repository\Eloquent;
//
//use App\LearningActivityProducing;
//use App\Student;
//
//class LearningActivityProducingRepository
//{
//    public function get(int $id): LearningActivityProducing
//    {
//        return LearningActivityProducing::findOrFail($id);
//    }
//
//    public function save(LearningActivityProducing $learningActivityProducing): bool
//    {
//        return $learningActivityProducing->save();
//    }
//
//    /**
//     * @return LearningActivityProducing[]
//     */
//    public function getActivitiesForStudent(Student $student): array
//    {
//        return $student->getCurrentWorkplaceLearningPeriod()->learningActivityProducing()
//            ->with('category', 'difficulty', 'status', 'resourcePerson', 'resourceMaterial', 'chain', 'feedback')
//            ->orderBy('date', 'DESC')
//            ->get()->all();
//    }
//
//    public function getActivitiesOfLastActiveDayForStudent(Student $student): array
//    {
//        /** @var LearningActivityProducing $lastActiveActivity */
//        $lastActiveActivity = $student->getCurrentWorkplaceLearningPeriod()->learningActivityProducing()->orderBy('date', 'DESC')->first();
//
//        if (!$lastActiveActivity) {
//            return [];
//        }
//
//        $dateOfLastActivity = $lastActiveActivity->date;
//
//        return $student->getCurrentWorkplaceLearningPeriod()->learningActivityProducing()
//            ->with('category', 'difficulty', 'status', 'resourcePerson', 'resourceMaterial', 'chain', 'feedback')
//            ->where('date', '=', $dateOfLastActivity)
//            ->get()->all();
//    }
//
//    public function delete(LearningActivityProducing $learningActivityProducing): bool
//    {
//        try {
//            $learningActivityProducing->feedback()->delete();
//
//            return $learningActivityProducing->delete();
//        } catch (\Exception $e) {
//            return false;
//        }
//    }
//
//    public function earliestActivityForStudent(Student $student): ?LearningActivityProducing
//    {
//        $activity = $student->getCurrentWorkplaceLearningPeriod()->learningActivityProducing()->orderBy('date',
//            'ASC')->first();
//
//        if (!$activity instanceof LearningActivityProducing && $activity !== null) {
//            throw new \RuntimeException('Expected result to be null or LearningActivityProducing, instead '.\get_class($activity));
//        }
//
//        return $activity;
//    }
//
//    public function latestActivityForStudent(Student $student): ?LearningActivityProducing
//    {
//        $activity = $student->getCurrentWorkplaceLearningPeriod()->learningActivityProducing()->orderBy('date',
//            'desc')->first();
//
//        if (!$activity instanceof LearningActivityProducing && $activity !== null) {
//            throw new \RuntimeException('Expected result to be null or LearningActivityProducing, instead '.\get_class($activity));
//        }
//
//        return $activity;
//    }
//}
