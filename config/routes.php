<?php
/** @var \Zend\Expressive\Application $app */

$app->route('/', App\Action\HomePageAction::class, ['GET', 'POST'], 'home');
