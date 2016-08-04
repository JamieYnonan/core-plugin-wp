<?php
namespace CorePluginWp\db;

/**
 * Interface QueryTransactionInterface
 * @package CorePluginWp\db
 */
interface QueryTransactionInterface
{
    public static function transaction();

    public static function commit();

    public static function rollback();
}