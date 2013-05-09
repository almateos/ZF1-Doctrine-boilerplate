<?php
/**
 * @author Frederik Eychenié <fred@multeegaming.com>
 */
namespace Documents;
/**
 * @MappedSuperclass
 */
abstract class AbstractEmbedded
{
    protected $_errors = array();

    /**
     * @abstract
     * @return bool
     */
    public abstract function isValid();

    /**
     * @param array $data
     */
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

    public function getErrors() {
        return $this->_errors;
    }
}