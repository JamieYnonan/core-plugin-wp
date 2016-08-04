<?php
namespace CorePluginWp;

/**
 * Interface ModelInterface
 * @package CorePluginWp
 */
interface ModelInterface
{
	public function validate();

	public function loadData(array $data, $loadPk = true);

	public function rules();
}