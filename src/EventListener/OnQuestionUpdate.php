<?php

namespace App\EventListener;

use App\Entity\Question;
use App\Entity\QuestionHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class OnQuestionUpdate
{
    public function postUpdate(Question $question, LifecycleEventArgs $args): void
    {
        /** @var EntityManager $em */
        $em = $args->getObjectManager();

        /** @var UnitOfWork $uow */
        $uow = $em->getUnitOfWork();

        $changeSet = $uow->getEntityChangeSet($question);

        $history = (new QuestionHistory)
            ->setQuestion($question)
            ->setTitle($changeSet['title'][0] ?? $question->getTitle())
            ->setStatus($changeSet['status'][0] ?? $question->getStatus());

        $question->addHistory($history);

        $em->persist($history);
        $em->flush();
    }
}