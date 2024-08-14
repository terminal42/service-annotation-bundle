<?php

declare(strict_types=1);

namespace Terminal42\ServiceAnnotationBundle\Annotation;

interface ServiceTagInterface
{
    public function getName(): string;

    /**
     * @return array<string, string|int|float|bool>
     */
    public function getAttributes(): array;
}
