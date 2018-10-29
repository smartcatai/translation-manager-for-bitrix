<?php

namespace Smartcat\ConnectorAPI\API\Resource;

use Joli\Jane\OpenApi\Client\QueryParam;
use Joli\Jane\OpenApi\Client\Resource;
class FastMTResource extends Resource
{
    /**
     * 
     *
     * @param \Smartcat\ConnectorAPI\API\Model\TranslateParams $translateParams Machine Translation order creation parameters
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Smartcat\ConnectorAPI\API\Model\TranslateResponse|\Smartcat\ConnectorAPI\API\Model\BadRequestBodyModel|\Smartcat\ConnectorAPI\API\Model\ErrorModel
     */
    public function fastMTTranslate(\Smartcat\ConnectorAPI\API\Model\TranslateParams $translateParams, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/fastMT';
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $this->serializer->serialize($translateParams, 'json');
        $request = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\TranslateResponse', 'json');
            }
            if ('400' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\BadRequestBodyModel', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }
}