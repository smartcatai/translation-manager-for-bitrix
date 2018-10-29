<?php

namespace Smartcat\ConnectorAPI\API\Resource;

use Joli\Jane\OpenApi\Client\QueryParam;
use Joli\Jane\OpenApi\Client\Resource;

class PricesResource extends Resource
{
    /**
     *
     *
     * @param array $parameters {
     * @var int $skip
     * @var int $take
     * @var string $type
     * }
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Smartcat\ConnectorAPI\API\Model\ServicePriceViewModel[]|\Smartcat\ConnectorAPI\API\Model\ErrorModel
     */
    public function pricesGetAccountPrices($parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setRequired('skip');
        $queryParam->setRequired('take');
        $queryParam->setDefault('type', NULL);
        $queryParam->setDefault('from', NULL);
        $queryParam->setDefault('to', NULL);
        $url = '/v0/prices/details';
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $queryParam->buildFormDataString($parameters);
        $request = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ServicePriceViewModel[]', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }
}