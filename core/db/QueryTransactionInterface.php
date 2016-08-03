<?php
namespace CorePluginWp;

/**
 * Interface QueryTransactionInterface
 * @package CorePluginWp
 */
interface QueryTransactionInterface
{
    public static function transaction();

    public static function commit();

    public static function rollback();
}