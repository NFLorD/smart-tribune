<?php

namespace App\Tests\Functional\Question;

use App\Controller\QuestionsController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends KernelTestCase
{
    private QuestionsController $controller;
    
    public function setUp(): void
    {
        self::bootKernel();
        
        $this->controller = self::$container->get(QuestionsController::class);
    }

    public function testSuccess(): void
    {
        $response = $this->callCreate('{
            "title": "Titre 1",
            "promoted": true,
            "status": "published",
            "answers": [{
                "channel": "faq",
                "body": "blahblahblah"
            }]
        }');

        $code = $response->getStatusCode();
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $code);
        $this->assertTrue(isset($json['id']));
    }

    public function testWrongStatus(): void
    {
        $response = $this->callCreate('{
            "title": "Titre 1",
            "promoted": true,
            "status": "new",
            "answers": [{
                "channel": "faq",
                "body": "blahblahblah"
            }]
        }');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testWrongChannel(): void
    {
        $response = $this->callCreate('{
            "title": "Titre 1",
            "promoted": true,
            "status": "draft",
            "answers": [{
                "channel": "faaq",
                "body": "blahblahblah"
            }]
        }');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function callCreate(string $payload): JsonResponse
    {
        $request = new Request([], [], [], [], [], [], $payload);

        return $this->controller->create($request, self::$container->get('serializer'));
    }
}
