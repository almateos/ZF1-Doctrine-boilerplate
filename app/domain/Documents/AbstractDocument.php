<?php
/**
 * @author Frederik Eychenié <frederik@almateos.com>
 */
namespace Documents;

use DomainExceptions\InvalidDocumentException as InvalidDocumentException,
    Doctrine\ODM\MongoDB\PersistentCollection,
    DateTime;

/**
 * @MappedSuperclass
 * @HasLifecylceCallbacks
 **/
abstract class AbstractDocument
{
    //region ############## MAPPED PROPERTIES #############

    /**
     * @Date
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @Date
     * @var \DateTime
     */
    protected $updated_at;

    //endregion

    //region ############# INTERNAL PROPERTIES ############

    /** @var array */
    protected $_errors = array();

    //endregion

    //region ################## ACCESSORS #################

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
     * @return AbstractDocument
     */
    public function setCreatedAt(DateTime $created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

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
     * @return AbstractDocument
     */
    public function setUpdatedAt(DateTime $updated_at)
    {
        $this->updated_at = $updated_at;
        return $this;
    }
    //endregion

    //region ############### UTILITY METHODS ##############

    /**
     * @abstract
     *
     * @return bool
     */
    abstract public function isValid();

    /** @param array $data */
    public function fromArray(array $data) {
        foreach($data as $key => $datum) {
            $method = 'set' . implode(array_map( 'ucfirst', explode('_', $key)));
            $this->$method($datum);
        }
    }

    /**
     * @return array
     */
    public function toArray($recursive = false) {
        $data = get_object_vars($this);
        if($recursive) {
            foreach($data as $key => $datum) {
                if($datum instanceof AbstractDocument || $datum instanceof AbstractEmbedded) $data[$key] = $datum->toArray(true);

                if($datum instanceof PersistentCollection) {
                    $subData = $datum->toArray();
                    foreach($subData as $subKey => $subDatum)  $subData[$subKey] = $subDatum->toArray(true);
                    $data[$key] = $subData;
                }
            }
        }
        unset($data['_errors']);
        return $data;
    }
    //endregion

    //region ############# LIFECYCLE CALLBACKS ############

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

    //endregion
}
