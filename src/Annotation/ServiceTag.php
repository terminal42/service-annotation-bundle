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

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
