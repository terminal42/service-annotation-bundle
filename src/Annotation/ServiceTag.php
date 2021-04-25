<?php

declare(strict_types=1);

/*
 * @copyright  Copyright (c) 2020, terminal42 gmbh
 * @author     terminal42 gmbh <https://terminal42.ch>
 * @license    MIT
 * @link       http://github.com/terminal42/service-annotation-bundle
 */

namespace Terminal42\ServiceAnnotationBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @Attributes({
 *     @Attribute("value", required = true, type = "string"),
 * })
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class ServiceTag implements ServiceTagInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param string|array $name
     */
    public function __construct($name, array $data = null)
    {
        if (\is_array($name) && null === $data) {
            // Instantiated by annotation reader
            $data = $name;

            $this->name = $data['value'];
            unset($data['value']);
        } else {
            // Instantiated using attributes
            if (!\is_string($name)) {
                throw new \InvalidArgumentException('Name must be a string.');
            }

            $this->name = $name;
        }

        $this->attributes = $data ?? [];
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
