<?php

use Zend\Expressive\Helper\ServerUrlMiddleware;
use Zend\Expressive\Helper\UrlHelperMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\ImplicitHeadMiddleware;
use Zend\Expressive\Router\Middleware\ImplicitOptionsMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Middleware\NotFoundHandler;
use Zend\Stratigility\Middleware\ErrorHandler;

/**
 * Setup middleware pipeline:
 */

/** @var \Zend\Expressive\Application $app */

// The error handler should be the first (most outer) middleware to catch
// all Exceptions.
$app->pipe(ErrorHandler::class);
$app->pipe(ServerUrlMiddleware::class);

// Register the routing middleware in the middleware pipeline
$app->pipe(RouteMiddleware::class);
$app->pipe(ImplicitHeadMiddleware::class);
$app->pipe(ImplicitOptionsMiddleware::class);
$app->pipe(UrlHelperMiddleware::class);

// Register the dispatch middleware in the middleware pipeline
$app->pipe(DispatchMiddleware::class);

// At this point, if no Response is returned by any middleware, the
// NotFoundHandler kicks in; alternately, you can provide other fallback
// middleware to execute.
$app->pipe(NotFoundHandler::class);
