<?php

declare(strict_types=1);

/*
 * @copyright  Copyright (c) 2020, terminal42 gmbh
 * @author     terminal42 gmbh <https://terminal42.ch>
 * @license    MIT
 * @link       http://github.com/terminal42/service-annotation-bundle
 */

namespace Terminal42\ServiceAnnotationBundle\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationException;
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

        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition->isAbstract() || $definition->isSynthetic()) {
                continue;
            }

            $class = $definition->getClass();

            // See Symfony\Component\DependencyInjection\Compiler\ResolveClassPass
            // Needs to be done here because this compiler pass runs before ResolveClassPass
            if (null === $class && preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+(?:\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+)++$/', $id)) {
                $class = $id;
            }

            $class = $container->getParameterBag()->resolveValue($class);

            if (
                !$class
                || null === ($reflection = $container->getReflectionClass($class, false))
                || $reflection->isAbstract()
            ) {
                continue;
            }

            $this->parseClassAnnotations($reflection, $definition);
            $this->parseMethodAnnotations($reflection, $definition);
        }
    }

    private function parseClassAnnotations(\ReflectionClass $reflection, Definition $definition): void
    {
        try {
            $annotations = $this->annotationReader->getClassAnnotations($reflection);
        } catch (AnnotationException $e) {
            // Ignore this class if annotations can't be parsed.
            return;
        }

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
            try {
                $annotations = $this->annotationReader->getMethodAnnotations($method);
            } catch (AnnotationException $e) {
                // Ignore this method if annotations can't be parsed.
                continue;
            }

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
