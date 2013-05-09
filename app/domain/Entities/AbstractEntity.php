<?php

namespace Entities;

use DomainExceptions\InvalidDocumentException as InvalidDocumentException,
    Doctrine\Common\Collections\ArrayCollection,
    DateTime;

/**
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 **/
abstract class AbstractEntity extends AbstractEntityBase
{

    /**
     *
     * @Column(type="datetime")
     */
    protected $updated_at;

    ############# LIFECYCLE CALLBACKS ############

    /**
     * @PrePersist
     *
     * @throws \DomainExceptions\InvalidDocumentException
     */
    public function prePersist() {
        if($this->created_at == null) $this->created_at = new DateTime();
        if($this->updated_at == null) $this->updated_at = new DateTime();
        if (!$this->isValid()) {
            throw new InvalidDocumentException();
        }
    }

    /**
     * @PreUpdate
     *
     * @throws \DomainExceptions\InvalidDocumentException
     */
    public function preUpdate() {
        $this->updated_at = new DateTime();
        if (!$this->isValid()) {
            throw new InvalidDocumentException();
        }
    }

    ################## ACCESSORS #################


    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTime $updated_at
     *
     * @return $this
     */
    public function setUpdatedAt(DateTime $updated_at)
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}
