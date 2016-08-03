<?php
namespace CorePluginWp;

/**
 * Class QueryBuilder
 * @package CorePluginWp
 */
class QueryBuilder implements QueryBulderInterface
{
    /**
     * @var string
     */
    private $select;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $where = '';

    /**
     * @var string
     */
    private $join = '';

    /**
     * @var string
     */
    private $groupBy = '';

    /**
     * @var string
     */
    private $having = '';

    /**
     * @var string
     */
    private $limit = '';

    /**
     * @var array
     */
    private $prepareValues = [];

    /**
     * @var array
     */
    private $typesJoin = ['JOIN', 'INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN'];

    /**
     * @var array
     */
    private $typesFormat = ['%s', '%f', '%d'];

    /**
     * @param string $params
     * @return $this
     */
    public function select($params = '*')
    {
        $this->select = 'SEELCT ' . $params;

        return $this;
    }

    /**
     * @param string|array $table
     *  examples:
     *   'wp_table_name'
     *   '\PluginWp\Models\Entity'
     *   ['wp_table_name', 'tb']
     *   ['\PluginWp\Models\Entity', 'etb']
     * @return $this
     */
    public function from($table)
    {
        $this->from = ' FROM '. $this->sanitizeTableName($table);
    }

    /**
     * @param array $where example, ['column_name', '=', 'value', '%s']
     * @return $this
     */
    public function where(array $where)
    {

        $this->where .= (empty($this->where)) ? ' WHERE' : ' AND';
        $this->where .= $this->sanitizeWhere($where);

        return $this;
    }

    /**
     * @param array $where example, ['column_name', '=', 'value', '%s']
     * @return $this
     */
    public function orWhere(array $where)
    {
        $this->where .= (empty($this->where)) ? ' WHERE' : ' OR';
        $this->where .= ' '. $this->sanitizeWhere($where);

        return $this;
    }

    /**
     * @param string $type
     * @param string|array $table
     *  examples:
     *   'wp_table_name'
     *   '\PluginWp\Models\Entity'
     *   ['wp_table_name', 'tb']
     *   ['\PluginWp\Models\Entity', 'etb']
     * @param $on
     * @return $this
     */
    public function join($type = 'JOIN', $table, $on)
    {
        $this->join .= ' '. $this->sanitizeTypeJoin($type)
            .' '. $this->sanitizeTableName($table) .' ON '. $on;

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function groupBy($column)
    {
        $this->groupBy .= (empty($this->groupBy)) ? ' GROUP BY' : ',';
        $this->groupBy .= ' '. $column;

        return $this;
    }

    /**
     * @param string $having
     * @return $this
     */
    public function having($having)
    {
        $this->having = ' HAVING '. $having;

        return $this;
    }

    /**
     * @param string $column
     * @param string $position
     * @return $this
     */
    public function orderBy($column, $position = 'ASC')
    {
        $this->orderBy = (empty($this->orderBy)) ? ' ORDER BY' : ',';
        $this->orderBy .= ' ' .$column .' '. $position;

        return $this;
    }

    /**
     * @param int $limit
     * @param int $offset default 0
     * @return $this
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit = ' LIMIT '. (string)$offset .', '. (string)$limit;

        return $this;
    }

    /**
     * @param $outputType
     *  OBJECT - result will be output as a numerically indexed array of row objects.
     *  OBJECT_K - result will be output as an associative array of row objects,
     *   using first column's values as keys (duplicates will be discarded).
     *  ARRAY_A - result will be output as a numerically indexed array of associative arrays,
     *   using column names as keys.
     *  ARRAY_N - result will be output as a numerically indexed array of numerically indexed arrays.
     * @return mixed
     * @throws \Exception
     */
    public function get($outputType = OBJECT)
    {
        $query = $this->sanitizeBlockQuery('select')
            . $this->sanitizeBlockQuery('from')
            . $this->join
            . $this->where
            . $this->groupBy;

        if (!empty($this->having) && empty($this->groupBy)) {
            throw new \Exception('must exits group by for use having');
        }

        $query .= $this->having
            . $this->orderBy
            . $this->limit;

        $queryPrepare = count($this->prepareValues) > 0
            ? $GLOBALS['wpdb']->prepare($query, $this->prepareValues)
            : $query;

        return $GLOBALS['wpdb']->get_results($queryPrepare, $outputType);
    }

    /**
     * @param string $type
     * @return string
     * @throws \Exception
     */
    private function sanitizeTypeJoin($type)
    {
        $type = strtoupper($type);
        if (!in_array($type, $this->typesJoin)) {
            throw new \Exception('invalid type for join: '. $type);
        }

        return $type;
    }

    /**
     * @param staring $table
     * @return string
     */
    private function sanitizeTableName($table)
    {
        $alias = '';
        if (is_array($table)) {
            $alias = $table[1];
            $table = $table[0];
        }

        return (class_exists($table)) ? $table::tableName() .$alias : $table .$alias;
    }

    /**
     * @param array $where
     * @return string
     */
    private function sanitizeWhere(array $where)
    {
        $whereOutput = ' '. $where[0] .' '. $where[1];
        if (isset($where[3]) && in_array($where[3], $this->typesFormat)) {
            $whereOutput .= ' '.$where[3];
            $this->prepareValues[] = $where[2];
        } else {
            $whereOutput .= ' '.$where[2];
        }

        return $whereOutput;
    }

    /**
     * @param $block
     * @return string
     * @throws \Exception
     */
    private function sanitizeBlockQuery($block)
    {
        if (empty($this->$block) || !is_string($block)) {
            throw new \Exception($block . ' can not be empty and must be string');
        }

        return $this->$block;
    }
}