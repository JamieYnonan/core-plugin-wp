<?php
namespace CorePluginWp;

class View
{
	/**
	 * @param file string argument.
	 * @param data an array argument.
	 */
	public static function render($file, array $data = [])
	{
		ob_start();
		extract($data);
		include __DIR__ . '/../../../../views/'. $file .'.php';
		return ob_get_clean();
	}

	/**
	 * @param file string argument.
	 * @param data an array argument.
	 */
	public static function renderJson($file, array $data = [])
	{
		$view = self::render($file, $data);
		self::json($view);
	}

	/**
	 * @param data an array argument.
	 */
	public static function json($data)
	{
		header('Content-type: application/json');
		echo json_encode($data);
		exit;
	}
}