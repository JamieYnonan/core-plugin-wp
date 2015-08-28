<?php
namespace CorePluginWp;

abstract class Controller
{
	protected function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}

	protected function isGet()
	{
		return $_SERVER['REQUEST_METHOD'] === 'GET';
	}

	protected function post($key = '')
	{
		if(empty($key)) {
			$post = filter_var_array($_POST, FILTER_SANITIZE_STRING);
		} else {
			$value = $_POST[$key];
			$post = is_array($value)
				? filter_var_array($value, FILTER_SANITIZE_STRING)
				: filter_var($value, FILTER_SANITIZE_STRING);;
		}
		return $post;
	}
}