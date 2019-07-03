<?php

namespace Terminal42\ServiceAnnotationBundle\Annotation;

interface ServiceTagInterface
{
    public function getName(): string;

    public function getAttributes(): array;
}
