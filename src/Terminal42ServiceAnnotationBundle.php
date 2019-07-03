<?php

namespace Terminal42\ServiceAnnotationBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Terminal42\ServiceAnnotationBundle\DependencyInjection\Compiler\ServiceAnnotationPass;

class Terminal42ServiceAnnotationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->registerForAutoconfiguration(ServiceAnnotationInterface::class)
            ->addTag('terminal42_service_annotation')
        ;

        $container->addCompilerPass(new ServiceAnnotationPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 99);
    }
}
