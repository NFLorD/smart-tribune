<?php

namespace App\Service;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CsvExporter
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    private SerializerInterface $serializer;
    private array $context;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        $this->context = [
            AbstractNormalizer::GROUPS => 'csv_export',
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback = 
                    fn ($date) => $date->format(CsvExporter::DATETIME_FORMAT),
                'updatedAt' => $dateCallback
            ],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => 
                fn ($entity) => $entity->getId()
        ];
    }

    public function export(array $entities, array $context = [])
    {
        return $this->serializer->serialize(
            $entities, 
            'csv',
            array_merge($this->context, $context)
        );
    }
}