<?php

namespace App\Tests\Functional\Question;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends KernelTestCase
{
    private int $questionId;

    public function setUp(): void
    {
        self::bootKernel();

        $em = self::$container->get('doctrine.orm.default_entity_manager');
        
        $question = (new Question)
            ->setTitle('Create')
            ->setStatus(Question::STATUS_DRAFT)
            ->setPromoted(false);
        $em->persist($question);
        $em->flush();

        $this->questionId = $question->getId();
    }

    public function getQuestion(): Question
    {
        return self::$container
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository(Question::class)
            ->find($this->questionId);
    }

    public function testSuccess(): void
    {
        $question = $this->getQuestion();

        $historyCount = count($question->getHistory());
        $title = $question->getTitle();
        $status = $question->getStatus();

        $response = $this->callUpdate('{
            "title": "Update 1",
            "status": "draft"
        }');

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $question = $this->getQuestion();

        $newHistoryCount = count($question->getHistory());
        $this->assertEquals($historyCount + 1, $newHistoryCount, 'No history entry was added.');

        $history = $question->getHistory()->get($newHistoryCount - 1);
        $this->assertEquals($title, $history->getTitle());
        $this->assertEquals($status, $history->getStatus());
    }

    public function testWrongStatus(): void
    {
        $response = $this->callUpdate('{
            "title": "Update 2",
            "status": "zzz"
        }');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function callUpdate(string $payload): JsonResponse
    {
        $request = new Request([], [], [], [], [], [
            'REQUEST_URI' => '/api/questions/'.$this->questionId,
            'REQUEST_METHOD' => 'PATCH'
        ], $payload);
        return static::$kernel->handle($request);
    }
}
