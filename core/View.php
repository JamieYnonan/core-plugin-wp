<?php
namespace CorePluginWp;

class View
{
	public static function render($file, array $data = [])
	{
		ob_start();
		extract($data);
		include __DIR__ . '/../../../../views/'. $file .'.php';
		return ob_get_clean();
	}

	public static function renderJson($file, array $data = [])
	{
		$view = self::render($file, $data);
		self::json($view);
	}

	public static function json($data)
	{
		header('Content-type: application/json');
		echo json_encode($data);
		exit;
	}
}