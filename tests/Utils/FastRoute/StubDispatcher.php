<?php
declare(strict_types=1);

namespace WoohooLabs\Harmony\Tests\Utils\FastRoute;

use FastRoute\Dispatcher;

class StubDispatcher implements Dispatcher
{
    private array $routeInfo;

    public function __construct(array $routeInfo = [])
    {
        $this->routeInfo = $routeInfo;
    }

    /**
     * Dispatches against the provided HTTP method verb and URI.
     *
     * Returns array with one of the following formats:
     *
     *     [self::NOT_FOUND]
     *     [self::METHOD_NOT_ALLOWED, ['GET', 'OTHER_ALLOWED_METHODS']]
     *     [self::FOUND, $handler, ['varName' => 'value', ...]]
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @return array
     */
    public function dispatch($httpMethod, $uri)
    {
        return $this->routeInfo;
    }
}
