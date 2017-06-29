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

namespace Dravencms\AdminModule\Components\InfoMessage\InfoMessageForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Locale\CurrentLocale;
use Dravencms\Model\InfoMessage\Entities\InfoMessage;
use Dravencms\Model\InfoMessage\Entities\InfoMessageTranslation;
use Dravencms\Model\InfoMessage\Repository\InfoMessageRepository;
use Dravencms\Model\InfoMessage\Repository\InfoMessageTranslationRepository;
use Dravencms\Model\Locale\Repository\LocaleRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;

/**
 * Class InfoMessageForm
 * @package Dravencms\AdminModule\Components\InfoMessage\InfoMessageForm
 */
class InfoMessageForm extends BaseControl
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var InfoMessageRepository */
    private $infoMessageRepository;

    /** @var InfoMessageTranslationRepository */
    private $infoMessageTranslationRepository;

    /** @var LocaleRepository */
    private $localeRepository;

    /** @var CurrentLocale */
    private $currentLocale;

    /** @var InfoMessage|null */
    private $infoMessage = null;

    /** @var array */
    public $onSuccess = [];

    /**
     * InfoMessageForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param EntityManager $entityManager
     * @param InfoMessageRepository $infoMessageRepository
     * @param InfoMessageTranslationRepository $infoMessageTranslationRepository
     * @param LocaleRepository $localeRepository
     * @param CurrentLocale $currentLocale
     * @param InfoMessage|null $infoMessage
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        EntityManager $entityManager,
        InfoMessageRepository $infoMessageRepository,
        InfoMessageTranslationRepository $infoMessageTranslationRepository,
        LocaleRepository $localeRepository,
        CurrentLocale $currentLocale,
        InfoMessage $infoMessage = null
    ) {
        parent::__construct();

        $this->infoMessage = $infoMessage;

        $this->currentLocale = $currentLocale;
        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->infoMessageRepository = $infoMessageRepository;
        $this->infoMessageTranslationRepository = $infoMessageTranslationRepository;
        $this->localeRepository = $localeRepository;


        if ($this->infoMessage) {
            $defaults = [
                'identifier' => $this->infoMessage->getIdentifier(),
                'isActive' => $this->infoMessage->isActive(),
                'type' => $this->infoMessage->getType(),
                'url' => $this->infoMessage->getUrl(),
                'fromDate' => ($this->infoMessage->getFromDate() ? $this->infoMessage->getFromDate()->format($this->currentLocale->getDateTimeFormat()) : null),
                'toDate' => ($this->infoMessage->getToDate() ? $this->infoMessage->getToDate()->format($this->currentLocale->getDateTimeFormat()) : null),
            ];

            foreach ($this->infoMessage->getTranslations() AS $translation)
            {
                $defaults[$translation->getLocale()->getLanguageCode()]['text'] = $translation->getText();
            }
        }
        else{
            $defaults = [
                'isActive' => true,
                'url' => '*'
            ];
        }

        $this['form']->setDefaults($defaults);
    }

    /**
     * @return \Dravencms\Components\BaseForm\BaseForm
     */
    protected function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            $container = $form->addContainer($activeLocale->getLanguageCode());
            $container->addTextArea('text')
                ->setRequired('Please enter info message text.')
                ->addRule(Form::MAX_LENGTH, 'Info message text is too long.', 4000);
        }

        $form->addText('identifier')
            ->setRequired('Please enter identifier');

        $form->addSelect('type', null, [
            InfoMessage::TYPE_DANGER => 'Danger',
            InfoMessage::TYPE_WARNING => 'Warning',
            InfoMessage::TYPE_INFO => 'Info',
            InfoMessage::TYPE_DEFAULT => 'Default',
            InfoMessage::TYPE_PRIMARY => 'Primary',
            InfoMessage::TYPE_SUCCESS => 'Success',
        ])
            ->setRequired('Please enter type');

        $form->addText('url');

        $form->addText('fromDate');

        $form->addText('toDate');

        $form->addCheckbox('isActive');

        $form->addSubmit('send');

        $form->onValidate[] = [$this, 'editFormValidate'];
        $form->onSuccess[] = [$this, 'editFormSucceeded'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function editFormValidate(Form $form)
    {
        $values = $form->getValues();

        if (!$this->infoMessageRepository->isIdentifierFree($values->identifier, $this->infoMessage)) {
            $form->addError('Tento identifier je již zabrán.');
        }

        if (!$this->presenter->isAllowed('infoMessage', 'edit')) {
            $form->addError('Nemáte oprávění editovat info message.');
        }
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function editFormSucceeded(Form $form)
    {
        $values = $form->getValues();

        if ($values->fromDate)
        {
            $fromDate = \DateTime::createFromFormat($this->currentLocale->getDateTimeFormat(), $values->fromDate);
        }
        else
        {
            $fromDate = null;
        }

        if ($values->toDate)
        {
            $toDate = \DateTime::createFromFormat($this->currentLocale->getDateTimeFormat(), $values->toDate);
        }
        else
        {
            $toDate = null;
        }

        if ($this->infoMessage) {
            $infoMessage = $this->infoMessage;
            $infoMessage->setIdentifier($values->identifier);
            $infoMessage->setUrl($values->url);
            $infoMessage->setType($values->type);
            $infoMessage->setIsActive($values->isActive);
            $infoMessage->setFromDate($fromDate);
            $infoMessage->setToDate($toDate);

        } else {
            $infoMessage = new InfoMessage(
                $values->identifier,
                $fromDate,
                $toDate,
                $values->type,
                $values->url,
                $values->isActive
            );
        }

        $this->entityManager->persist($infoMessage);
        $this->entityManager->flush();


        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            if ($infoMessageTranslation = $this->infoMessageTranslationRepository->getTranslation($infoMessage, $activeLocale))
            {
                $infoMessageTranslation->setText($values->{$activeLocale->getLanguageCode()}->text);
            }
            else
            {
                $infoMessageTranslation = new InfoMessageTranslation(
                    $infoMessage,
                    $activeLocale,
                    $values->{$activeLocale->getLanguageCode()}->text
                );
            }

            $this->entityManager->persist($infoMessageTranslation);
        }

        $this->entityManager->flush();

        $this->onSuccess();
    }


    public function render()
    {
        $template = $this->template;
        $template->activeLocales = $this->localeRepository->getActive();
        $template->setFile(__DIR__ . '/InfoMessageForm.latte');
        $template->render();
    }
}