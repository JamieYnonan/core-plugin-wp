<?php
namespace CorePluginWp;

abstract class Controller
{
	/**
	 * @return boolean
	 */
	protected function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}

	/**
	 * @return boolean
	 */
	protected function isGet()
	{
		return $_SERVER['REQUEST_METHOD'] === 'GET';
	}

	/**
	 * @param key string argument
	 * @return false if not exists the key in $_POST
	 * @return value of key or all $_POST sanitized
	 */
	protected function post($key = null)
	{
		if($key === null) {
			$post = filter_var_array($_POST, FILTER_SANITIZE_STRING);
		} elseif (is_string($key) && isset($_POST[$key])) {
			$value = $_POST[$key];
			$post = is_array($value)
				? filter_var_array($value, FILTER_SANITIZE_STRING)
				: filter_var($value, FILTER_SANITIZE_STRING);
		} else {
			return false;
		}
		return $post;
	}

	protected function isAjax()
	{
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}
}