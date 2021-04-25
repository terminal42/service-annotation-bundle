<?php

declare(strict_types=1);

namespace Terminal42\ServiceAnnotationBundle\Tests\Fixtures;

use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;

/**
 * @ServiceTag("target_service", priority=123, bar="baz")
 */
class ServiceWithAnnotations
{
    /**
     * @ServiceTag("target_service", foo="foobar")
     */
    public function doSomething(): void
    {
    }
}
