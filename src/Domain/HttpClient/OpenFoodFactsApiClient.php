<?php

namespace App\Domain\HttpClient;

use App\Domain\HttpClient\Exception\ApiUnreachableException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenFoodFactsApiClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire('%open_food_facts.api_base_url%')]
        private readonly string $baseUrl,
    ) {
    }

    /**
     * @throws ApiUnreachableException
     */
    public function searchProducts(array $filters = []): array
    {
        try {
            $response = $this->httpClient->request(Request::METHOD_GET, $this->baseUrl . '/api/v2/search', [
                'query' => array_merge([
                    'page_size' => 5,
                    'json' => true,
                    'fields' => 'product_name,countries,code',
                ], $filters),
            ]);

            return $response->toArray();
        } catch(TransportExceptionInterface $exception) {
            $this->logger->critical('Open Food Facts API request failed: ' . $exception->getMessage());
            throw new ApiUnreachableException($exception->getMessage());
        } catch (HttpExceptionInterface $exception) {
            $this->logger->error('Open Food Facts API returned an error: ' . $exception->getMessage());
            throw new ApiUnreachableException($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->logger->warning('An unexpected error occurred while accessing Open Food Facts API: ' . $exception->getMessage());
        }
    }
}
