<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\InfoMessageModule;

use Dravencms\AdminModule\Components\InfoMessage\InfoMessageForm\InfoMessageFormFactory;
use Dravencms\AdminModule\Components\InfoMessage\InfoMessageGrid\InfoMessageGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\InfoMessage\Entities\InfoMessage;
use Dravencms\Model\InfoMessage\Repository\InfoMessageRepository;


/**
 * Class InfoMessagePresenter
 * @package Dravencms\AdminModule\InfoMessageModule
 */
class InfoMessagePresenter extends SecuredPresenter
{
    /** @var InfoMessageRepository @inject */
    public $infoMessageRepository;

    /** @var InfoMessageFormFactory @inject */
    public $infoMessageFormFactory;

    /** @var InfoMessageGridFactory @inject */
    public $infoMessageGridFactory;

    /** @var null|InfoMessage */
    private $infoMessage = null;

    /**
     * @isAllowed(infoMessage,edit)
     */
    public function renderDefault()
    {
        $this->template->h1 = 'Info messages';
    }

    /**
     * @isAllowed(infoMessage,edit)
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit($id)
    {
        if ($id) {
            $this->template->h1 = 'Edit info message';
            $infoMessage = $this->infoMessageRepository->getOneById($id);
            if (!$infoMessage) {
                $this->error();
            }
            $this->infoMessage = $infoMessage;
        } else {
            $this->template->h1 = 'New info message';
        }
    }

    public function createComponentInfoMessageForm()
    {
        $control = $this->infoMessageFormFactory->create($this->infoMessage);
        $control->onSuccess[] = function()
        {
            $this->flashMessage('Info message has been successfully saved', 'alert-success');
            $this->redirect("InfoMessage:");
        };

        return $control;
    }


    public function createComponentInfoMessageGrid()
    {
        $control = $this->infoMessageGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Info message has been successfully deleted', 'alert-success');
            $this->redirect('InfoMessage:');
        };
        return $control;
    }

}
