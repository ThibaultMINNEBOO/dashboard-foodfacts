<?php

namespace App\Domain\Widget\OpenFoodFacts;

use App\Domain\Enums\WidgetType;
use App\Domain\HttpClient\Exception\ApiUnreachableException;
use App\Domain\HttpClient\OpenFoodFactsApiClient;
use App\Domain\Widget\ValueObject\WidgetError;
use App\Domain\Widget\ValueObject\WidgetResponseInterface;
use App\Domain\Widget\ValueObject\WidgetResult;
use App\Domain\Widget\WidgetDataProviderInterface;

class ProductCountByCountriesWidget implements WidgetDataProviderInterface
{
    public function __construct(
        private readonly OpenFoodFactsApiClient $apiClient,
    ) {

    }

    public function supports(WidgetType $type): bool
    {
        return WidgetType::PRODUCT_COUNT_BY_COUNTRIES === $type;
    }

    public function getData(array $configuration): WidgetResponseInterface
    {
        try {
            $products = $this->apiClient->searchProducts([
                'countries_tags_en' => $configuration['country'],
            ]);

            return WidgetResult::create($products['count'] ?? 0);
        } catch (ApiUnreachableException $e) {
            return WidgetError::create("L'API OpenFoodFacts est injoignable.");
        } catch (\Throwable $e) {
            return WidgetError::create("Une erreur est survenue lors de la récupération des données.");
        }
    }
}
