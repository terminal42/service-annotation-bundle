# terminal42/service-annotation-bundle

This bundle allows to add tags to container services using annotations.
Similar to services subscribers for events, this allows the class to contain
all necessary information within the same file.

This is most helpful if you use autowiring and autoconfiguration in your
service definitions, but it works without it (e.g. for bundles) nonetheless.


## Installation

```bash
$ composer.phar require terminal42/service-annotation-bundle ^1.0
```

Afterwards, make sure to enable the
`Terminal42\ServiceAnnotation\Terminal42ServiceAnnotationBundle` in your
kernel.

## Configuration

The bundle currently does not provide any service configuration.


## How to use

Annotations can be used on any service which is registered in the container.

The example annotations below equal to the following tags:

```yaml
service:
    App\EventListener\KernelListener:
        tags:
            - { name: monolog.logger, channel: routing }
            - { name: kernel.event_listener, event: kernel.request, method: onKernelRequest }
```

**Example:**

```php
// src/EventListener/KernelListener.php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;

/**
 * @ServiceTag("monolog.logger", channel="routing")
 */
class KernelListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @ServiceTag("kernel.event_listener", event="kernel.request")
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->logger->debug('Request for '.$event->getRequest()->getRequestUri());
    }
}
```

If an annotation is added to a method instead of the class, the method name
is automatically added to the service tag "method" argument.


## Extending the annotations

If your bundle provides new tags to other services, you can improve
<abbr title="Developer Experience">DX</abbr> by providing your own
annotations. Good IDEs like PhpStorm can then provide autocomplete support.

**Example:**

```php

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTagInterface;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("channel", type = "string", required = true),
 * })
 */
class Logger implements ServiceTagInterface
{
    public $channel;

    public function getName(): string
    {
        return 'monolog.logger';
    }

    public function getAttributes(): array
    {
        return ['channel' => $this->channel];
    }
}
```

Applying this to the example above, the class annotation can be
simplified like this:

```php
// src/EventListener/KernelListener.php

namespace App\EventListener;

/**
 * @Logger(channel="routing")
 */
class KernelListener
{
    // Same class as before
}
```
