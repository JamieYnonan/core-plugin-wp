<?php
namespace CorePluginWp;

interface CronJobInterface
{
	public function activation();

	public function desactivation();
}