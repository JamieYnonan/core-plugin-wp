<?php
namespace CorePluginWp;

/**
 * Interface EntityInterface
 * @package CorePluginWp
 */
interface EntityInterface
{
    public static function tableName();

    public function save();

    public function delete();
}