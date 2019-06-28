<?php

namespace Terminal42\ServiceAnnotationBundle\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\CachedReader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;

class ServiceAnnotationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('annotation_reader')) {
            return;
        }

        /** @var CachedReader $annotationReader */
        $annotationReader = $container->get('annotation_reader');

        $services = array_keys($container->findTaggedServiceIds('terminal42_service_annotation'));

        /** @var Definition $definition */
        foreach ($services as $service) {
            $definition = $container->getDefinition($service);
            $definition->clearTag('terminal42_service_annotation');

            $reflection = new \ReflectionClass($definition->getClass());

            if ($reflection->isAbstract()) {
                continue;
            }

            $annotations = $annotationReader->getClassAnnotations($reflection);

            foreach ($annotations as $annotation) {
                if (!$annotation instanceof ServiceTag) {
                    continue;
                }

                $definition->addTag($annotation->getName(), $annotation->getAttributes());
            }
        }
    }
}
