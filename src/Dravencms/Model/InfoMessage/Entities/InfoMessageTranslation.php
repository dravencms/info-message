<?php
namespace Dravencms\Model\InfoMessage\Entities;

use Doctrine\ORM\Mapping as ORM;
use Dravencms\Model\Locale\Entities\Locale;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class InfoMessageTranslation
 * @package App\Model\InfoMessage\Entities
 * @ORM\Entity
 * @ORM\Table(name="infoMessageInfoMessageTranslation")
 */
class InfoMessageTranslation extends Nette\Object
{
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="text",nullable=false)
     */
    private $text;

    /**
     * @var InfoMessage
     * @ORM\ManyToOne(targetEntity="InfoMessage", inversedBy="translations")
     * @ORM\JoinColumn(name="info_message_id", referencedColumnName="id")
     */
    private $infoMessage;

    /**
     * @var Locale
     * @ORM\ManyToOne(targetEntity="Dravencms\Model\Locale\Entities\Locale")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id")
     */
    private $locale;

    /**
     * InfoMessageTranslation constructor.
     * @param InfoMessage $infoMessage
     * @param Locale $locale
     * @param $text
     */
    public function __construct(InfoMessage $infoMessage, Locale $locale, $text)
    {
        $this->text = $text;
        $this->infoMessage = $infoMessage;
        $this->locale = $locale;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param InfoMessage $infoMessage
     */
    public function setInfoMessage(InfoMessage $infoMessage)
    {
        $this->infoMessage = $infoMessage;
    }

    /**
     * @param Locale $locale
     */
    public function setLocale(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return InfoMessage
     */
    public function getInfoMessage()
    {
        return $this->infoMessage;
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }
}

