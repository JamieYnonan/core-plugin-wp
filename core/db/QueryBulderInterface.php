<?php
namespace CorePluginWp;

/**
 * Interface QueryBulderInterface
 * @package CorePluginWp
 */
interface QueryBulderInterface
{
    public function select($select = '*');

    public function from($table);

    public function where(array $where);

    public function orWhere(array $where);

    public function join($type = 'join', $table, $on);

    public function groupBy($column);

    public function having($having);

    public function orderBy($column, $position);

    public function limit($limit);
}