<?php

declare(strict_types=1);

/*
 * Password Validation Bundle for Contao Open Source CMS.
 *
 * @copyright  Copyright (c) 2019, terminal42 gmbh
 * @author     terminal42 gmbh <https://terminal42.ch>
 * @license    MIT
 * @link       http://github.com/terminal42/service-annotation-bundle
 */

namespace Terminal42\ServiceAnnotationBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Terminal42\ServiceAnnotationBundle\DependencyInjection\Compiler\ServiceAnnotationPass;

class Terminal42ServiceAnnotationBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container
            ->registerForAutoconfiguration(ServiceAnnotationInterface::class)
            ->addTag('terminal42_service_annotation')
        ;

        $container->addCompilerPass(new ServiceAnnotationPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 99);
    }
}
