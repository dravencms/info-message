<?php
namespace Dravencms\Model\InfoMessage\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class InfoMessage
 * @package App\Model\InfoMessage\Entities
 * @ORM\Entity
 * @ORM\Table(name="infoMessageInfoMessage")
 */
class InfoMessage extends Nette\Object
{
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_DEFAULT = 'default';
    const TYPE_PRIMARY = 'primary';
    const TYPE_DANGER = 'danger';
    const TYPE_WARNING = 'warning';

    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false,unique=true)
     */
    private $identifier;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false,unique=true)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false,unique=true)
     */
    private $url;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fromDate;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $toDate;

    /**
     * @var ArrayCollection|InfoMessageTranslation[]
     * @ORM\OneToMany(targetEntity="InfoMessageTranslation", mappedBy="infoMessage",cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * InfoMessage constructor.
     * @param $identifier
     * @param \DateTimeInterface|null $fromDate
     * @param \DateTimeInterface|null $toDate
     * @param string $type
     * @param null $url
     * @param bool $isActive
     */
    public function __construct($identifier, \DateTimeInterface $fromDate = null, \DateTimeInterface $toDate = null, $type = self::TYPE_INFO, $url = null, $isActive = true)
    {
        $this->identifier = $identifier;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->type = $type;
        $this->url = $url;
        $this->isActive = $isActive;

        $this->translations = new ArrayCollection();
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param \DateTimeInterface $fromDate
     */
    public function setFromDate(\DateTimeInterface $fromDate = null)
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @param \DateTimeInterface $toDate
     */
    public function setToDate(\DateTimeInterface $toDate = null)
    {
        $this->toDate = $toDate;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return ArrayCollection|InfoMessageTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getToDate()
    {
        return $this->toDate;
    }
}

