<?php
namespace CorePluginWp;

/**
 * Class Controller
 * @package CorePluginWp
 */
abstract class Controller
{
    /**
     * @var Response
     */
	protected $response;

    /**
     * @var Request
     */
	protected $request;

    /**
     * Controller constructor.
     * @param Response $response
     * @param Request|null $request
     */
    public function __construct(
    	Response $response,
    	Request $request = null
    ) {
    	$request = ($request === null) ? new Request : $request;
        $this->request = $request;
        $this->response = $response;
    }
}
