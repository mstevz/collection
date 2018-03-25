<?php

/**
 * Allows the creation of a collection giving an easier data manipulation.
 * @author mstevz <myeaaaah@gmail.com>
 * @license https://github.com/mstevz/Collection/blob/master/LICENSE MIT License
 */
class Collection extends \ArrayObject implements \JsonSerializable {

    # Properties

    /**
    * Property that contains the index position of each associate key.
    * @var array
    */
    private $indexMap;

    /**
    * Property that contains the collection values, accessible by the associative key.
    * @var array
    */
    private $container;

    /**
    * Property that represents the number of existing items.
    * @var int
    */
    private $count;

    # Constructor
    public function __construct(){
        $this->indexMap = [];
        $this->container = [];
        $this->count = 0;
    }

    # Public Methods

    /**
    * Returns the index of a given associative key.
    * @param string $key
    */
    public function indexOf(string $key){
        foreach($this->indexMap as $index => $value){
            if($key == $value)
                return $index;
        }
    }

    /**
    * Add's a new value to the collection.
    * @param string $key
    * @param object $value
    * @return Collection
    */
    public function add(string $offset, $value) : Collection {
        if($this->offsetExists($offset))
            throw new \Exception("Cannot duplicate a key entry: \"{$offset}\"");

        $this->indexMap[$this->count++]= $offset;
        $this->container[$offset] = $value;

        return $this;
    }

    /**
    * Removes an existing value.
    * @param string $key
    * @return Collection
    */
    public function remove(string $offset) : Collection {
        unset($this->indexMap[$this->indexOf($offset)]);
        $this->indexMap = array_values($this->indexMap);

        unset($this->container[$offset]);
        $this->count--;

        return $this;
    }

    /**
     * Returns if given offset exists or not.
     * @param  string|int $offset
     * @return bool
     */
    public function offsetExists($offset) : bool {
        return isset($this->container[$offset]);
    }

    /**
     * Behaviour for when operator [] is used to retrieve any value from the
     * collection.
     * @param string $offset
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /**
     * Behaviour for when operator [] is used in as new value allocation.
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        $this->add($offset, $value);
    }

    /**
     * Behaviour for when operator [] is used in unset() function.
     * @param string|int $offset
     */
    public function offsetUnset($offset) {
        $this->remove($offset);
    }

    /**
    * Returns the size of the collection.
    * @return int
    */
    public function count() : int {
        return $this->count;
    }

    /**
    * Searches for the given associative key or index.
    * @return object
    */
    public function get($key){
        if(gettype($key) == "integer" && isset($this->indexMap[$key]))
            $key = $this->indexMap[$key];

        return ($this->container[$key]) ?? null;
    }

    /**
     * Returns entire collection as array.
     * @return array
     */
    public function getAll() : array {
        return $this->container;
    }

    /**
     * Returns all existing offsets name values.
     * @return array [description]
     */
    public function getOffsets() : array {
        return array_keys($this->container);
    }

    /**
     * Iterates the collection and executes callable object with the current
     * value as argument.
     *
     * Returns any return value from the callable object.
     *
     * @param  callable $callback
     * @return array                Returned values from callable object.
     */
    public function each(callable $callback) : array {
        $buffer = [];

        foreach($this->_data as $index => $value){
            $buffer[] .= $callback($index, $value);
        }

        return $buffer;
    }

    /**
    * Returns the collection values as json string.
    *
    * @return string
    */
    public function toJson() : string {
        return json_encode($this->container);
    }

    /**
    * Adds data from json values.
    *
    * @param string $json
    * @param bool $override Choose to replace or add the data.
    **/
    public function fromJson(string $json, bool $override = false) {
        $value = json_decode($json);
        $this->container = ($override) ? $value : $this->container . $value;
    }

    /**
     * Converts object into serialized string.
     * @return string
     */
    public function serialize() {
       return serialize($this->container);
    }

    /**
     * Converts serialized string into new object.
     * @param  string $string
     */
    public function unserialize($string) {
       $this->container = unserialize($string);
    }

    /**
    * Allows this class to be json serializable.
    * @return array
    */
    public function jsonSerialize() : array {
        return $this->container;
    }

    /**
    * Allows this class to be iteratable.
    * @return ArrayIterator
    */
    public function getIterator() : \ArrayIterator {
        return new \ArrayIterator($this->container);
    }

}
?>
