<?php
namespace CorePluginWp;

/**
 * Interface CronJobInterface
 * @package CorePluginWp
 */
interface CronJobInterface
{
	public function activation();

	public function desactivation();
}