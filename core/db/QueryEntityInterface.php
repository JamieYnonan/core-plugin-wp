<?php
namespace CorePluginWp;

/**
 * Interface QueryInterface
 * @package CorePluginWp
 */
interface QueryEntityInterface
{
    public static function findOne($pk);

    public static function firstOrModel();
}