<?php

namespace ABBYY\CloudAPI\API\Resource;

use Joli\Jane\OpenApi\Client\QueryParam;
use Joli\Jane\OpenApi\Client\Resource;

class OrderResource extends Resource
{
    /**
     *
     *
     * @param \ABBYY\CloudAPI\API\Model\SubmitOrderModel $model Order creation parameters
     * @param array $parameters List of parameters
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\ABBYY\CloudAPI\API\Model\FullOrderViewModel|\ABBYY\CloudAPI\API\Model\BadRequestBodyModel|\ABBYY\CloudAPI\API\Model\ErrorModel
     */
    public function orderSubmitOrder(\ABBYY\CloudAPI\API\Model\SubmitOrderModel $model, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/order';
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru', 'Content-Type' => 'application/json'), $queryParam->buildHeaders($parameters));
        $body = $this->serializer->serialize($model, 'json');
        //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/' . preg_replace('/[^a-z0-9]/', '', $url) . '.txt', $body . PHP_EOL, FILE_APPEND);
        $request = $this->messageFactory->createRequest('POST', $url, $headers, $body);

        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\FullOrderViewModel', 'json');
            }
            if ('400' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\BadRequestBodyModel', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }

    /**
     *
     *
     * @param string $id Order ID
     * @param array $parameters List of parameters
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\ABBYY\CloudAPI\API\Model\ErrorModel
     */
    public function orderDeleteOrder($id, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/order/{id}';
        $url = str_replace('{id}', urlencode($id), $url);
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $queryParam->buildFormDataString($parameters);
        $request = $this->messageFactory->createRequest('DELETE', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }

    /**
     *
     *
     * @param string $id Order ID
     * @param array $parameters List of parameters
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\ABBYY\CloudAPI\API\Model\FullOrderViewModel|\ABBYY\CloudAPI\API\Model\ErrorModel
     */
    public function orderGetOrder($id, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/order/{id}';
        $url = str_replace('{id}', urlencode($id), $url);
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $queryParam->buildFormDataString($parameters);
        $request = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\FullOrderViewModel', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }

    /**
     *
     *
     * @param \ABBYY\CloudAPI\API\Model\GetProposalModel $model Order calculation parameters
     * @param array $parameters List of parameters
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\ABBYY\CloudAPI\API\Model\ProposalViewModel[]|\ABBYY\CloudAPI\API\Model\BadRequestBodyModel|\ABBYY\CloudAPI\API\Model\ErrorModel
     */
    public function orderGetQuotes(\ABBYY\CloudAPI\API\Model\GetProposalModel $model, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/order/quotes';
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $this->serializer->serialize($model, 'json');

        $request = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ProposalViewModel[]', 'json');
            }
            if ('204' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ProposalViewModel[]', 'json');
            }
            if ('206' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ProposalViewModel[]', 'json');
            }
            if ('400' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\BadRequestBodyModel', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }

    /**
     *
     *
     * @param \ABBYY\CloudAPI\API\Model\GetOrdersPageModel $model
     * @param array $parameters List of parameters
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\ABBYY\CloudAPI\API\Model\OrdersPageViewModel|\ABBYY\CloudAPI\API\Model\ErrorModel
     */
    public function orderGetOrders(\ABBYY\CloudAPI\API\Model\GetOrdersPageModel $model, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/order/all';
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $this->serializer->serialize($model, 'json');
        $request = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\OrdersPageViewModel', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string)$response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }
}