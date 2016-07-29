<?php
namespace CorePluginWp;

/**
 * Interface ModelInterface
 * @package CorePluginWp
 */
interface ModelInterface
{
	public static function tableName();

	public function validate();

	public function loadData(array $data);

	public function rules();

	public function save();

	public function delete();
}