<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\QuestionHistoryRepository;
use App\Repository\QuestionRepository;
use App\Service\CsvExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/questions")
 */
class QuestionsController extends AbstractController
{
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    #[Route('', methods: ['POST'], name: 'question_create')]
    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $question = $serializer->deserialize(
            $request->getContent(), Question::class, 'json',
            ['object_to_populate' => new Question]
        );

        $errors = $this->validator->validate($question);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new JsonResponse($errorsString, 400);
        }

        $this->entityManager->persist($question);
        $this->entityManager->flush();

        return new JsonResponse(['id' => $question->getId()], 201);
    }

    #[Route('/{id}', methods: ['PATCH'], name: 'question_update')]
    public function update(Question $question, Request $request): JsonResponse
    {
        $questionData = json_decode($request->getContent(), true);

        $question
            ->setTitle($questionData['title'] ?? null)
            ->setStatus($questionData['status'] ?? null);

        $errors = $this->validator->validate($question);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new JsonResponse($errorsString, 400);
        }

        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/{id}/history.csv', methods: ['GET'], name: 'question_history_export')]
    public function export(
        Question $question,
        QuestionHistoryRepository $historyRepository, 
        CsvExporter $csvExporter
    ): Response 
    {
        $history = $historyRepository->findBy(['question' => $question->getId()]);
        $csv = $csvExporter->export($history);

        return new Response($csv, 200);
    }
}