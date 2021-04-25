<?php

declare(strict_types=1);

/*
 * @copyright  Copyright (c) 2020, terminal42 gmbh
 * @author     terminal42 gmbh <https://terminal42.ch>
 * @license    MIT
 * @link       http://github.com/terminal42/service-annotation-bundle
 */

namespace Terminal42\ServiceAnnotationBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Terminal42\ServiceAnnotationBundle\DependencyInjection\Compiler\ServiceAnnotationPass;
use Terminal42\ServiceAnnotationBundle\Tests\Fixtures\ServiceWithAnnotations;
use Terminal42\ServiceAnnotationBundle\Tests\Fixtures\ServiceWithAttributes;

class ServiceAnnotationPassTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require __DIR__.'/../../Fixtures/ServiceWithAnnotations.php';
        require __DIR__.'/../../Fixtures/ServiceWithAttributes.php';
    }

    public function testRegistersServicesFromAnnotations(): void
    {
        $container = new ContainerBuilder();

        $container
            ->register('annotation_reader')
            ->setClass(AnnotationReader::class)
            ->setArguments([new DocParser()])
        ;

        $container
            ->register('test_service')
            ->setClass(ServiceWithAnnotations::class)
        ;

        $container
            ->register('target_service')
        ;

        (new ServiceAnnotationPass())->process($container);

        $this->assertSame($container->findTaggedServiceIds('target_service'), [
            'test_service' => [
                ['priority' => 123, 'bar' => 'baz'],
                ['foo' => 'foobar', 'method' => 'doSomething'],
            ],
        ]);
    }

    public function testRegistersServicesFromAttributes(): void
    {
        if (\PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Attributes support requires PHP>=8.0.');
        }

        $container = new ContainerBuilder();

        $container
            ->register('test_service')
            ->setClass(ServiceWithAttributes::class)
        ;

        $container
            ->register('target_service')
        ;

        (new ServiceAnnotationPass())->process($container);

        $this->assertSame($container->findTaggedServiceIds('target_service'), [
            'test_service' => [
                ['priority' => 123, 'bar' => 'baz'],
                ['foo' => 'foobar', 'method' => 'doSomething'],
            ],
        ]);
    }
}
