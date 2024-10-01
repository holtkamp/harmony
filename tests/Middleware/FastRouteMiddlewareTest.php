<?php

declare(strict_types=1);

namespace WoohooLabs\Harmony\Tests\Middleware;

use FastRoute\Dispatcher;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Harmony\Exception\FastRouteException;
use WoohooLabs\Harmony\Exception\MethodNotAllowed;
use WoohooLabs\Harmony\Exception\RouteNotFound;
use WoohooLabs\Harmony\Harmony;
use WoohooLabs\Harmony\Middleware\FastRouteMiddleware;
use WoohooLabs\Harmony\Tests\Utils\Controller\DummyController;
use WoohooLabs\Harmony\Tests\Utils\FastRoute\StubDispatcher;
use WoohooLabs\Harmony\Tests\Utils\Psr7\DummyResponse;
use WoohooLabs\Harmony\Tests\Utils\Psr7\DummyServerRequest;

class FastRouteMiddlewareTest extends TestCase
{
    public function testConstruct(): void
    {
        $middleware = new FastRouteMiddleware(new StubDispatcher());

        $fastRoute = $middleware->getFastRoute();

        $this->assertInstanceOf(StubDispatcher::class, $fastRoute);
    }

    public function testGetFastRouteWhenNull(): void
    {
        $middleware = new FastRouteMiddleware();

        $this->expectException(FastRouteException::class);

        $middleware->getFastRoute();
    }

    public function testSetFastRoute(): void
    {
        $middleware = new FastRouteMiddleware(null);

        $middleware->setFastRoute(new StubDispatcher());

        $this->assertInstanceOf(StubDispatcher::class, $middleware->getFastRoute());
    }

    public function testProcessRouteNotFound(): void
    {
        $harmony = $this->createHarmony();
        $middleware = new FastRouteMiddleware(new StubDispatcher([Dispatcher::NOT_FOUND]));

        $this->expectException(RouteNotFound::class);

        $middleware->process($harmony->getRequest(), $harmony);
    }

    public function testProcessMethodNotAllowed(): void
    {
        $harmony = $this->createHarmony();
        $middleware = new FastRouteMiddleware(new StubDispatcher([Dispatcher::METHOD_NOT_ALLOWED]));

        $this->expectException(MethodNotAllowed::class);

        $middleware->process($harmony->getRequest(), $harmony);
    }

    public function testProcessWhenNull(): void
    {
        $harmony = $this->createHarmony();
        $middleware = new FastRouteMiddleware();

        $this->expectException(FastRouteException::class);

        $middleware->process($harmony->getRequest(), $harmony);
    }

    public function testProcess(): void
    {
        $harmony = $this->createHarmony();
        $middleware = new FastRouteMiddleware(new StubDispatcher([Dispatcher::FOUND, [DummyController::class, "dummyAction"], []]));

        $middleware->process($harmony->getRequest(), $harmony);

        $this->assertEquals([DummyController::class, "dummyAction"], $harmony->getRequest()->getAttribute("__action"));
    }

    public function testProcessAttributesPassed(): void
    {
        $harmony = $this->createHarmony();
        $middleware = new FastRouteMiddleware(new StubDispatcher([Dispatcher::FOUND, ["", ""], ["arg1" => "val1", "arg2" => "val2"]]));

        $middleware->process($harmony->getRequest(), $harmony);

        $this->assertEquals("val1", $harmony->getRequest()->getAttribute("arg1"));
        $this->assertEquals("val2", $harmony->getRequest()->getAttribute("arg2"));
    }

    private function createHarmony(): Harmony
    {
        return new Harmony(new DummyServerRequest(), new DummyResponse());
    }
}
