<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuestionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $question = (new Question)
            ->setTitle('Question 1')
            ->setStatus(Question::STATUS_DRAFT)
            ->setPromoted(false)
            ->addAnswer((new Answer)
                ->setChannel(Answer::CHANNEL_FAQ)
                ->setBody('Réponse 1')
            );
        $manager->persist($question);

        $manager->flush();

        # Updating the title in order to create history entries
        $question->setTitle('Question 2');
        $manager->flush();

        $question->setTitle('Question 3');
        $manager->flush();
    }
}
