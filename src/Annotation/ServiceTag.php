<?php

namespace Terminal42\ServiceAnnotationBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("name", required = true, type = "string"),
 *     @Attribute("attributes", type = "array"),
 * })
 */
class ServiceTag
{
    /** @var string */
    protected $name;

    /**
     * @var array
     */
    protected $attributes = [];

    public function __construct(array $values)
    {
        $this->name = $values['name'] ?? null;
        $this->attributes = $values['attributes'] ?? [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
