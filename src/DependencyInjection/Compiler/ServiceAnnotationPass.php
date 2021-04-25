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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTagInterface;

class ServiceAnnotationPass implements CompilerPassInterface
{
    /**
     * @var bool
     */
    private $supportsAttributes;

    /**
     * @var Reader|null
     */
    private $annotationReader;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $this->supportsAttributes = \PHP_VERSION_ID >= 80000;

        if (!$container->has('annotation_reader') && !$this->supportsAttributes) {
            return;
        }

        $this->annotationReader = $container->get('annotation_reader', ContainerInterface::NULL_ON_INVALID_REFERENCE);

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
        $annotations = [];

        if (null !== $this->annotationReader) {
            try {
                $annotations = $this->annotationReader->getClassAnnotations($reflection);
            } catch (AnnotationException $e) {
                // Ignore class annotations if they can't be parsed.
            }
        }

        if ($this->supportsAttributes) {
            foreach ($reflection->getAttributes() as $attribute) {
                $annotations[] = $attribute->newInstance();
            }
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
            $annotations = [];

            if (null !== $this->annotationReader) {
                try {
                    $annotations = $this->annotationReader->getMethodAnnotations($method);
                } catch (AnnotationException $e) {
                    // Ignore method annotations if they can't be parsed.
                }
            }

            if ($this->supportsAttributes) {
                foreach ($method->getAttributes() as $attribute) {
                    $annotations[] = $attribute->newInstance();
                }
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
