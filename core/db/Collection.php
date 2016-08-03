<?php
namespace CorePluginWp\db;


class Colection implements \Iterator
{
    /**
     * @var array
     */
    private $colection = [];

    /**
     * @var int
     */
    private $position = 0;

    /**
     * Colection constructor.
     * @param array $entities
     * @param $entityClass
     */
    public function __construct(array $entities, $entityClass)
    {
        foreach ($entities as $entity) {
            $this->colection[] = new $entityClass($entity);
        }
    }

    /**
     * @return instanceof entityClass
     */
    public function current()
    {
        return $this->colection[$this->position];
    }

    /**
     * @return void
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->array[$this->position]);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }
}