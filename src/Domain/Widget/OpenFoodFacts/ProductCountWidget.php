<?php

namespace App\Domain\Widget\OpenFoodFacts;

use App\Domain\Enums\WidgetType;
use App\Domain\HttpClient\Exception\ApiUnreachableException;
use App\Domain\HttpClient\OpenFoodFactsApiClient;
use App\Domain\Widget\ValueObject\WidgetError;
use App\Domain\Widget\ValueObject\WidgetResponseInterface;
use App\Domain\Widget\ValueObject\WidgetResult;
use App\Domain\Widget\WidgetDataProviderInterface;

class ProductCountWidget implements WidgetDataProviderInterface
{
    public function __construct(
        private OpenFoodFactsApiClient $apiClient,
    ) {
    }

    public function supports(WidgetType $type): bool
    {
        return $type === WidgetType::PRODUCT_COUNT;
    }

    public function getData(array $configuration): WidgetResponseInterface
    {
        try {
            $products = $this->apiClient->searchProducts();

            return WidgetResult::create($products['count'] ?? 0);
        } catch (ApiUnreachableException $e) {
            return WidgetError::create("L'API OpenFoodFacts est injoignable.");
        } catch (\Throwable $e) {
            return WidgetError::create("Une erreur est survenue lors de la récupération des données.");
        }
    }
}
