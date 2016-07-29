<?php
namespace CorePluginWp;

class View
{
    /**
     * @param $file string if $pluginDir = null, $file must be absolute route + filename and extension, else only filename
     * @param array $data
     * @param null $pluginDir
     * @return string
     */
	public static function render($file, array $data = [], $pluginDir = null)
	{
		ob_start();
		extract($data);
        $view = ($pluginDir === null) ? $file : $pluginDir . 'views/'. $file .'.php';
		include $view;
		return ob_get_clean();
	}
}