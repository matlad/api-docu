<?php

/**
 * @copyright   Copyright (c) 2016 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\ApiDocu;

use Nette;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http;
use Ublaboo\ApiRouter\ApiRoute;
use Nette\Application\Routers\RouteList;
use Ublaboo\Responses\JsonPrettyResponse;

class Starter extends Nette\Object
{

	const API_DOCU_STARTER_QUERY_KEY_TARGET   = '__apiDocu';
	const API_DOCU_STARTER_QUERY_KEY_GENERATE = '__apiDocuGenerate';

	/**
	 * @var Generator
	 */
	private $generator;

	/**
	 * @var IRouter
	 */
	private $router;

	/**
	 * @var Http\Response
	 */
	private $response;

	/**
	 * @var Http\Request
	 */
	private $httpRequest;


	public function __construct(
		Generator $generator,
		IRouter $router,
		Http\Response $response,
		Http\Request $httpRequest
	) {
		if (!($router instanceof RouteList)) {
			return;
		}

		$this->generator = $generator;
		$this->router = $router;

		$this->response = $response;
		$this->httpRequest = $httpRequest;

		foreach ($router as $route) {
			if ($route instanceof ApiRoute) {
				$route->onMatch[] = [$this, 'routeMatched'];
			}
		}
	}


	public function routeMatched(ApiRoute $route, Request $request)
	{
		if (NULL !== ($format = $request->getParameter(self::API_DOCU_STARTER_QUERY_KEY_GENERATE))) {
			$this->generator->generateAll($this->router);

			exit(0);
		}

		if (NULL !== ($format = $request->getParameter(self::API_DOCU_STARTER_QUERY_KEY_TARGET))) {
			$this->generator->generateTarget($route, $request);

			exit(0);
		}
	}

}