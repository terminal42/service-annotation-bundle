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
     * @param string|array $serviceName
     */
    public function __construct($serviceName, ...$data)
    {
        if (\is_array($serviceName) && 0 === \count($data)) {
            // Instantiated by annotation reader
            $data = $serviceName;

            $this->name = $data['value'];
            unset($data['value']);
        } else {
            // Instantiated using attributes
            if (!\is_string($serviceName)) {
                throw new \InvalidArgumentException('Name must be a string.');
            }

            $this->name = $serviceName;
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
