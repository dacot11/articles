<?php

namespace App\Action;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use DaveChild\TextStatistics as TS;

class HomePageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $template = $container->get(TemplateRendererInterface::class);
        $analyzer = $container->get(TS\TextStatistics::class);

        return new HomePageAction($template, $analyzer);
    }
}
