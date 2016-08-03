<?php
namespace CorePluginWp;

/**
 * Class QueryTransaction
 * @package CorePluginWp
 */
class QueryTransaction implements QueryTransactionInterface
{
    /**
     * @return void
     */
    public static function transaction()
    {
        $GLOBALS['wpdb']->query('START TRANSACTION');
    }

    /**
     * @return void
     */
    public static function commit()
    {
        $GLOBALS['wpdb']->query('COMMIT');
    }

    /**
     * @return void
     */
    public static function rollback()
    {
        $GLOBALS['wpdb']->query('ROLLBACK');
    }
}