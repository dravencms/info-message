<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\InfoMessage\Repository;

use Dravencms\Model\InfoMessage\Entities\InfoMessage;
use Kdyby\Doctrine\EntityManager;
use Nette;

class InfoMessageRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $infoMessageRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * MenuRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->infoMessageRepository = $entityManager->getRepository(InfoMessage::class);
    }

    /**
     * @param $id
     * @return mixed|null|InfoMessage
     */
    public function getOneById($id)
    {
        return $this->infoMessageRepository->find($id);
    }

    /**
     * @param $id
     * @return InfoMessage[]
     */
    public function getById($id)
    {
        return $this->infoMessageRepository->findBy(['id' => $id]);
    }

    /**
     * @param bool $isInOverview
     * @return InfoMessage[]
     */
    public function getByInOverview($isInOverview = true)
    {
        return $this->infoMessageRepository->findBy(['isInOverview' => $isInOverview]);
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getInfoMessageQueryBuilder()
    {
        $qb = $this->infoMessageRepository->createQueryBuilder('im')
            ->select('im');
        return $qb;
    }

    /**
     * @return InfoMessage[]
     */
    public function getActive()
    {
        $now = new \DateTime();
        return $this->infoMessageRepository->createQueryBuilder('im')
            ->select('im')
            ->where('im.isActive = :isActive')
            ->andWhere('im.fromDate > :fromDate OR im.fromDate IS NULL')
            ->andWhere('im.toDate < :toDate OR im.toDate IS NULL')
            ->setParameters([
                'isActive' => true,
                'fromDate' => $now,
                'toDate' => $now
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $identifier
     * @param InfoMessage|null $infoMessageIgnore
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isIdentifierFree($identifier, InfoMessage $infoMessageIgnore = null)
    {
        $qb = $this->infoMessageRepository->createQueryBuilder('im')
            ->select('im')
            ->where('im.identifier = :identifier')
            ->setParameters([
                'identifier' => $identifier
            ]);

        if ($infoMessageIgnore)
        {
            $qb->andWhere('im != :infoMessageIgnore')
                ->setParameter('infoMessageIgnore', $infoMessageIgnore);
        }

        $query = $qb->getQuery();
        return (is_null($query->getOneOrNullResult()));
    }

    /**
     * @param array $parameters
     * @return InfoMessage|null
     */
    public function getOneByParameters(array $parameters)
    {
        return $this->infoMessageRepository->findOneBy($parameters);
    }

    /**
     * @return InfoMessage[]
     */
    public function getAll()
    {
        return $this->infoMessageRepository->findAll();
    }
}