<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\InfoMessage\Repository;

use Dravencms\Model\InfoMessage\Entities\InfoMessage;
use Dravencms\Model\InfoMessage\Entities\InfoMessageTranslation;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Dravencms\Model\Locale\Entities\ILocale;

class InfoMessageTranslationRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $infoMessageTranslationRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * MenuRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->infoMessageTranslationRepository = $entityManager->getRepository(InfoMessageTranslation::class);
    }

    /**
     * @param InfoMessage $gallery
     * @param ILocale $locale
     * @return InfoMessageTranslation
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTranslation(InfoMessage $gallery, ILocale $locale)
    {
        $qb = $this->infoMessageTranslationRepository->createQueryBuilder('gt')
            ->select('gt')
            ->where('gt.locale = :locale')
            ->andWhere('gt.gallery = :gallery')
            ->setParameters([
                'gallery' => $gallery,
                'locale' => $locale
            ]);
        return $qb->getQuery()->getOneOrNullResult();
    }
}