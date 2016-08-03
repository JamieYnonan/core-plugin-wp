<?php
namespace CorePluginWp;

/**
 * Class QueryEntity
 * @package CorePluginWp
 */
abstract class QueryEntity implements QueryEntityInterface
{
    /**
     * @return static
     */
    public static function firstOrModel()
    {
        $data = $GLOBALS['wpdb']->get_row(
            "SELECT * FROM {static::tableName()} LIMIT 1",
            ARRAY_A
        );

        return ($data === null) ? new static() : new static($data);
    }

    /**
     * @param int $pk
     * @return static
     * @throws \Exception
     */
    public static function findOne($pk)
    {
        $data = $GLOBALS['wpdb']->get_row($GLOBALS['wpdb']->prepare(
            "SELECT * FROM {static::tableName()} WHERE {static::$pk} = %d LIMIT 1",
            $pk
        ), ARRAY_A);

        if ($data === null) {
            throw new \Exception('not exists element '. static::$pk .' = '. $pk);
        }

        return new static($data);
    }
}