<?php

declare(strict_types=1);

namespace Terminal42\ServiceAnnotationBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 *
 * @Target({"CLASS", "METHOD"})
 *
 * @Attributes({
 *     @Attribute("value", required = true, type = "string"),
 * })
 */
class ServiceTag implements ServiceTagInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [];

    /**
     * @param array<string, string|int|float|bool> $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['value'];

        unset($data['value']);

        $this->attributes = $data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
