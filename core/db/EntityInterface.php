<?php
namespace CorePluginWp\db;

/**
 * Interface EntityInterface
 * @package CorePluginWp\db
 */
interface EntityInterface
{
    public static function tableName();

    public function save();

    public function delete();
}