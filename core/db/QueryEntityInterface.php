<?php
namespace CorePluginWp\db;

/**
 * Interface QueryEntityInterface
 * @package CorePluginWp\db
 */
interface QueryEntityInterface
{
    public function findOne($pk);

    public function firstOrModel();
}