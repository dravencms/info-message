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
     * @param InfoMessage $infoMessage
     * @param ILocale $locale
     * @return InfoMessageTranslation
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTranslation(InfoMessage $infoMessage, ILocale $locale)
    {
        $qb = $this->infoMessageTranslationRepository->createQueryBuilder('imt')
            ->select('imt')
            ->where('imt.locale = :locale')
            ->andWhere('imt.infoMessage = :infoMessage')
            ->setParameters([
                'infoMessage' => $infoMessage,
                'locale' => $locale
            ]);
        return $qb->getQuery()->getOneOrNullResult();
    }
}