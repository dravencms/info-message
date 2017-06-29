<?php

namespace Dravencms\FrontModule\Components\InfoMessage\InfoMessage\Detail;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Locale\CurrentLocale;
use Dravencms\Model\InfoMessage\Repository\InfoMessageRepository;
use Dravencms\Model\InfoMessage\Repository\InfoMessageTranslationRepository;

/**
 * Class Detail
 * @package Dravencms\FrontModule\Components\InfoMessage\InfoMessage\Detail
 */
class Detail extends BaseControl
{
    /** @var CurrentLocale */
    private $currentLocale;

    /** @var InfoMessageRepository */
    private $infoMessageRepository;

    /** @var InfoMessageTranslationRepository */
    private $infoMessageTranslationRepository;

    public function __construct(
        InfoMessageRepository $infoMessageRepository,
        InfoMessageTranslationRepository $infoMessageTranslationRepository,
        CurrentLocale $currentLocale
    )
    {
        parent::__construct();
        $this->infoMessageTranslationRepository = $infoMessageTranslationRepository;
        $this->infoMessageRepository = $infoMessageRepository;
        $this->currentLocale = $currentLocale;
    }

    public function render()
    {
        $template = $this->template;


        $template->setFile(__DIR__.'/detail.latte');
        $template->render();
    }
}
