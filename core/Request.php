<?php
namespace CorePluginWp;

/**
 * Class Request
 * @package CorePluginWp
 */
class Request
{
    /**
     * @return bool
     */
	public function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}

    /**
     * @return bool
     */
	public function isGet()
	{
		return $_SERVER['REQUEST_METHOD'] === 'GET';
	}

    /**
     * @return bool
     */
	public function isAjax()
	{
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}

    /**
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed|null
     */
	public function post($key = null, $default = null)
	{
		if ($key === null) {
			return filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		} elseif (isset($_POST[$key])) {
			return (is_array($_POST[$key]))
				? filter_var_array($_POST[$key], FILTER_SANITIZE_STRING)
				: filter_var($_POST[$key], FILTER_SANITIZE_STRING);
		}
		return $default;
	}

    /**
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed|null
     */
	public function get($key = null, $default = null)
	{
		if ($key === null) {
			return filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
		} elseif (isset($_GET[$key])) {
			return (is_array($_GET[$key]))
				? filter_var_array($_GET[$key], FILTER_SANITIZE_STRING)
				: filter_var($_GET[$key], FILTER_SANITIZE_STRING);
		}
		return $default;
	}
}