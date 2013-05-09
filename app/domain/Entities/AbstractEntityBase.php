<?php

namespace Entities;

use DomainExceptions\InvalidDocumentException as InvalidDocumentException,
    Doctrine\Common\Collections\ArrayCollection,
    DateTime;

/**
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 **/
abstract class AbstractEntityBase
{

    /**
     *
     * @Column(type="datetime")
     */
    protected $created_at;


    /** @var array */
    protected $_errors = array();

    /**
     * @abstract
     * @return bool
     */

    ############# LIFECYCLE CALLBACKS ############

    /**
     * @PrePersist
     *
     * @throws \DomainExceptions\InvalidDocumentException
     */
    public function prePersist() {

        if($this->created_at == null) $this->created_at = new DateTime();
        if (!$this->isValid()) {
            throw new InvalidDocumentException();
        }
    }

    ################## ACCESSORS #################

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     *
     * @return $this
     */
    public function setCreatedAt(DateTime $created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }


    abstract public function isValid();

    /** @param array $data */
    public function fromArray(array $data) {
        foreach($data as $key => $datum) {
            $method = 'set' . implode(array_map( 'ucfirst', explode('_', $key)));
            if(method_exists($this, $method)) $this->$method($datum);
            else trigger_error('No method: "' . $method . '" for "' . get_class($this) . " object.");
        }
    }

    /** @return array */
    public function getErrors() {
        return $this->_errors;
    }


    /**
     * @return array
     */
    public function toArray($recursive = false) {
        $data = get_object_vars($this);
        if($recursive) {
            foreach($data as $key => $datum) {
                if($datum instanceof AbstractDocument || $datum instanceof AbstractEmbedded) $data[$key] = $datum->toArray(true);

                if($datum instanceof ArrayCollection || $datum instanceof \Doctrine\ORM\PersistentCollection) {
                    $subData = $datum->toArray();
                    foreach($subData as $subKey => $subDatum)  $subData[$subKey] = $subDatum->toArray(true);
                    $data[$key] = $subData;
                }
                if(is_object($datum)) { 
                    //if(method_exists($datum, 'toArray')) $datum = $datum->toArray();
                    if(method_exists($datum, 'getId')) $datum = $datum->getId();
                    else $datum = get_class($datum);
                    $data[$key] = $datum;
                }
            }
        }
        unset($data['_errors']);
        return $data;
    }
}




