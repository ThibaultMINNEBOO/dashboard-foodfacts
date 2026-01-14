<?php

namespace App\Application\UseCase;

use App\Domain\Entity\Widget;
use App\Domain\Widget\ValueObject\WidgetResponseInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class ResolveWidgetData
{
    public function __construct(
        #[AutowireIterator('app.widget_data_provider')]
        private iterable $widgetDataProviders,
    ) {

    }

    public function execute(Widget $widget): WidgetResponseInterface
    {
        foreach ($this->widgetDataProviders as $provider) {
            if ($provider->supports($widget->getType())) {
                return $provider->getData($widget->getConfiguration());
            }
        }

        throw new \RuntimeException('No suitable widget data provider found.');
    }
}
