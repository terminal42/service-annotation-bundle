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

namespace Terminal42\ServiceAnnotationBundle\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTagInterface;

class ServiceAnnotationPass implements CompilerPassInterface
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('annotation_reader')) {
            return;
        }

        $this->annotationReader = $container->get('annotation_reader');

        $services = array_keys($container->findTaggedServiceIds('terminal42_service_annotation'));

        /* @var Definition $definition */
        foreach ($services as $service) {
            $definition = $container->getDefinition($service);
            $definition->clearTag('terminal42_service_annotation');

            $reflection = new \ReflectionClass($definition->getClass());

            if ($reflection->isAbstract()) {
                continue;
            }

            $this->parseClassAnnotations($reflection, $definition);
            $this->parseMethodAnnotations($reflection, $definition);
        }
    }

    private function parseClassAnnotations(\ReflectionClass $reflection, Definition $definition): void
    {
        $annotations = $this->annotationReader->getClassAnnotations($reflection);

        foreach ($annotations as $annotation) {
            if (!$annotation instanceof ServiceTagInterface) {
                continue;
            }

            $definition->addTag($annotation->getName(), $annotation->getAttributes());
        }
    }

    private function parseMethodAnnotations(\ReflectionClass $reflection, Definition $definition): void
    {
        foreach ($reflection->getMethods() as $method) {
            $annotations = $this->annotationReader->getMethodAnnotations($method);

            foreach ($annotations as $annotation) {
                if (!$annotation instanceof ServiceTagInterface) {
                    continue;
                }

                $attributes = $annotation->getAttributes();
                $attributes['method'] = $method->getName();

                $definition->addTag($annotation->getName(), $attributes);
            }
        }
    }
}
