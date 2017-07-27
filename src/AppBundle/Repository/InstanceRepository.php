<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Instance;
use AppBundle\Entity\Schema;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * InstanceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstanceRepository extends EntityRepository
{

    /**
     * @param Schema $schema
     * @param User $user
     * @return Instance[]
     */
    public function findFromSchemaAndUser(Schema $schema, User $user)
    {
        return $this->findBy([
            'schema' => $schema,
            'createdBy' => $user
        ], [
            'createdAt' => 'DESC'
        ]);
    }

    /**
     * @param User $user
     * @return Instance[]
     */
    public function findLatestFromUser(User $user)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.createdBy = :u')
            ->orderBy('i.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->execute([
                'u' => $user
            ]);
    }

    /**
     * @param Tag $tag
     * @return Instance[]
     */
    public function findFromTag(Tag $tag)
    {
        return $this->createQueryBuilder('i')
            ->join('i.tags', 't')
            ->where('t = :t')
            ->orderBy('i.createdAt')
            ->getQuery()
            ->execute([
                't' => $tag
            ]);
    }

}
