<?php

namespace App\Repository;

use App\Entity\GeneratorStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GeneratorStats>
 *
 * @method GeneratorStats|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeneratorStats|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeneratorStats[]    findAll()
 * @method GeneratorStats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeneratorStatsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeneratorStats::class);
    }

    public function add(GeneratorStats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GeneratorStats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getGeneratorStatsInDatePeriodQueryBuilder(int $generatorId, string $from, string $to): QueryBuilder {
        return $this->createQueryBuilder('gs')
            ->select('gs, g.name')
            ->leftJoin('App\Entity\Generator', 'g', 'WITH', 'g.id = gs.generator_id')
            ->orderBy('gs.date', 'ASC')
            ->addOrderBy('gs.hour', 'ASC')
            ->where('gs.generator_id = '.$generatorId) // powinienem przekazać parametr jako tablicę ['generatorId' => $generatorId]
            ->andWhere('gs.date >= '."'$from'".' AND gs.date <= '."'$to'");
    }

    public function getDailyGeneratorsStatsInMW(string $date) {
        return $this->createQueryBuilder('gs')
            ->select('gs.generator_id, g.name, (gs.average_power * 0.001) AS power_MW, gs.hour, gs.date')
            ->leftJoin('App\Entity\Generator', 'g', 'WITH', 'g.id = gs.generator_id')
            ->where('gs.date = '."'$date'")
            ->orderBy('gs.hour', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return GeneratorStats[] Returns an array of GeneratorStats objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GeneratorStats
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
