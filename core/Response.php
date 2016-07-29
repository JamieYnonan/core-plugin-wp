<?php
namespace CorePluginWp;

/**
 * Class Response
 * @package CorePluginWp
 */
class Response
{
    /**
     * @var string
     */
	private $pluginDir;

    /**
     * Response constructor.
     * @param $pluginDir string
     */
	public function __construct($pluginDir)
	{
		$this->pluginDir = $pluginDir;
	}

    /**
     * @param $file string only filename
     * @param array $data
     * @return string
     */
	public function view($file, array $data = [])
	{
	    return View::render($file, $data, $this->pluginDir);
	}

    /**
     * @param array $data
     */
	public function json(array $data)
	{
		wp_send_json($data);
	}
}