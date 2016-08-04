<?php
namespace CorePluginWp\db;

/**
 * Class QueryEntity
 * @package CorePluginWp\db
 */
abstract class QueryEntity implements QueryEntityInterface
{
    /**
     * @var string enitityClass
     */
    private $entityClass;

    /**
     * QueryEntity constructor.
     * @param string $entityClass
     */
    public function __construct($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return instanceof entityClass
     */
    public function firstOrModel()
    {
        $data = (new QueryBuilder())
            ->from($this->entityClass)
            ->limit(1)
            ->get();

        return ($data === null) ? new $this->entityClass : new $this->entityClass($data[0]);
    }

    /**
     * @param int $pk
     * @return instanceof entityClass
     * @throws \Exception
     */
    public function one($pk)
    {
        $data = (new QueryBuilder())
            ->from($this->entityClass)
            ->where([($this->entityClass)::$pk, '=', $pk])
            ->limit(1)
            ->get();

        if (count($data) != 1) {
            throw new \Exception('not exists element '. ($this->entityClass)::$pk .' = '. $pk);
        }

        return new $this->entityClass($data[0]);
    }

    /**
     * @param null|string $where
     * @return Collection
     */
    public function all($where = null)
    {
        $data = (new QueryBuilder())
            ->from($this->entityClass);

        if ($where !== null) {
            $data->where($where);
        }

        return new Collection($data->get(), $this->entityClass);
    }
}