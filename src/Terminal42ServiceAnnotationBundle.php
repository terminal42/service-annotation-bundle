<?php

declare(strict_types=1);

/*
 * @copyright  Copyright (c) 2020, terminal42 gmbh
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

        // Priority must be higher than ResolveInstanceofConditionalsPass so annotations
        // are added before autoconfiguration adds tags for interfaces etc.
        // See Symfony\Component\DependencyInjection\Compiler\PassConfig
        $container->addCompilerPass(new ServiceAnnotationPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 110);
    }
}
