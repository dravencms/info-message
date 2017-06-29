<?php

/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Dravencms\AdminModule\Components\InfoMessage\InfoMessageGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Locale\CurrentLocale;
use Dravencms\Model\InfoMessage\Repository\InfoMessageRepository;
use Kdyby\Doctrine\EntityManager;

/**
 * Class InfoMessageGrid
 * @package Dravencms\AdminModule\Components\InfoMessage\InfoMessageGrid
 */
class InfoMessageGrid extends BaseControl
{

    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var InfoMessageRepository */
    private $infoMessageRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var CurrentLocale */
    private $currentLocale;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * InfoMessageGrid constructor.
     * @param InfoMessageRepository $infoMessageRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param CurrentLocale $currentLocale
     */
    public function __construct(InfoMessageRepository $infoMessageRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager, CurrentLocale $currentLocale)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->infoMessageRepository = $infoMessageRepository;
        $this->entityManager = $entityManager;
        $this->currentLocale = $currentLocale;
    }


    /**
     * @param $name
     * @return \Dravencms\Components\BaseGrid\BaseGrid
     */
    public function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->infoMessageRepository->getInfoMessageQueryBuilder());

        $grid->addColumnText('identifier', 'Identifier')
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnDate('updatedAt', 'Last edit', $this->currentLocale->getDateTimeFormat())
            ->setSortable()
            ->setFilterDate();
        $grid->getColumn('updatedAt')->cellPrototype->class[] = 'center';

        $grid->addColumnDate('fromDate', 'From date', $this->currentLocale->getDateTimeFormat())
            ->setSortable()
            ->setFilterDate();
        $grid->getColumn('fromDate')->cellPrototype->class[] = 'center';

        $grid->addColumnDate('toDate', 'To date', $this->currentLocale->getDateTimeFormat())
            ->setSortable()
            ->setFilterDate();
        $grid->getColumn('toDate')->cellPrototype->class[] = 'center';

        $grid->addColumnText('type', 'Type')
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnBoolean('isActive', 'Active');

        if ($this->presenter->isAllowed('infoMessage', 'edit')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('infoMessage', 'delete')) {
            $grid->addActionHref('delete', 'Smazat', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($row) {
                    return ['Opravdu chcete smazat info message %s ?', $row->getIdentifier()];
                });


            $operations = ['delete' => 'Smazat'];
            $grid->setOperation($operations, [$this, 'gridOperationsHandler'])
                ->setConfirm('delete', 'Opravu chcete smazat %i info messages ?');
        }
        $grid->setExport();

        return $grid;
    }

    /**
     * @param $action
     * @param $ids
     */
    public function gridOperationsHandler($action, $ids)
    {
        switch ($action)
        {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function handleDelete($id)
    {
        $inforMessages = $this->infoMessageRepository->getById($id);
        foreach ($inforMessages AS $inforMessage)
        {
            $this->entityManager->remove($inforMessage);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/InfoMessageGrid.latte');
        $template->render();
    }
}
