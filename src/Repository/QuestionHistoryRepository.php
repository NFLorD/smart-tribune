<?php

namespace App\Repository;

use App\Entity\QuestionHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuestionHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionHistory[]    findAll()
 * @method QuestionHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionHistory::class);
    }
}
