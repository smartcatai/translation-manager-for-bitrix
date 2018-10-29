<?php

namespace Smartcat\ConnectorAPI\API\Resource;

use Joli\Jane\OpenApi\Client\QueryParam;
use Joli\Jane\OpenApi\Client\Resource;
class FileResource extends Resource
{
    /**
     * <b>Supported formats:</b> <br/> .html; .htm; .inx; .json; .pdf; .bmp; .dcx; .pcx; .png; .jp2; .jpc; .jpg; .jpeg; .jfif; .tif; .tiff; .gif; .djvu; .djv; .jb2; .php; .inc; .po; .resx; .zip; .docx; .doc; .txt; .rtf; .odt; .ppt; .pptx; .potx; .pps; .ppsx; .odp; .srt; .xlsx; .xls; .tjson; .ttx; .sdlxliff; .xlf; .xliff; .xml
     *
     * @param array  $parameters {
     *     @var string $exportFormat 
     *     @var string $quality 
     *     @var string $synthesisMode 
     *     @var array $languages 
     *     @var  $file 
     * }
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Smartcat\ConnectorAPI\API\Model\FileInfoViewModel[]|\Smartcat\ConnectorAPI\API\Model\ErrorModel
     */
    public function fileUploadFile($parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('exportFormat', NULL);
        $queryParam->setDefault('quality', NULL);
        $queryParam->setDefault('synthesisMode', NULL);
        $queryParam->setDefault('languages', NULL);
        $queryParam->setDefault('file', NULL);
        $queryParam->setFormParameters(array('file'));
        $url = '/v0/file';
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $queryParam->buildFormDataString($parameters);
        $request = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\FileInfoViewModel[]', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }
    /**
     * 
     *
     * @param string $id File ID
     * @param string $token File access key
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Smartcat\ConnectorAPI\API\Model\ErrorModel
     */
    public function fileDeleteFile($id, $token, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/file/{id}/{token}';
        $url = str_replace('{id}', urlencode($id), $url);
        $url = str_replace('{token}', urlencode($token), $url);
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $queryParam->buildFormDataString($parameters);
        $request = $this->messageFactory->createRequest('DELETE', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }
    /**
    * Swagger UI does not support file download! <br />
               However, you can open the browser console, copy the generated request to download a file and place it in the adjacent tab.
    *
    * @param string $id File ID
    * @param string $token File access key
    * @param array  $parameters List of parameters
    * @param string $fetch      Fetch mode (object or response)
    *
    * @return \Psr\Http\Message\ResponseInterface|\Smartcat\ConnectorAPI\API\Model\BadRequestBodyModel|\Smartcat\ConnectorAPI\API\Model\ErrorModel
    */
    public function fileDownloadFile($id, $token, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/file/{id}/{token}';
        $url = str_replace('{id}', urlencode($id), $url);
        $url = str_replace('{token}', urlencode($token), $url);
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $queryParam->buildFormDataString($parameters);
        $request = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('400' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\BadRequestBodyModel', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }
    /**
    * Swagger UI does not support file download! <br />
               However, you can open the browser console, copy the generated request to download a file and place it in the adjacent tab.  <br /><b> Works with docx files! </b>
    *
    * @param string $id File ID
    * @param string $token File access key
    * @param array  $parameters {
    *     @var array $resize Format the document pages need to be converted to
    * }
    * @param string $fetch      Fetch mode (object or response)
    *
    * @return \Psr\Http\Message\ResponseInterface|\Smartcat\ConnectorAPI\API\Model\BadRequestBodyModel|\Smartcat\ConnectorAPI\API\Model\ErrorModel
    */
    public function fileDownloadPdf($id, $token, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setRequired('resize');
        $url = '/v0/file/{id}/{token}/pdf';
        $url = str_replace('{id}', urlencode($id), $url);
        $url = str_replace('{token}', urlencode($token), $url);
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $queryParam->buildFormDataString($parameters);
        $request = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('400' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\BadRequestBodyModel', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }
    /**
     * 
     *
     * @param string $id File ID
     * @param string $token File access key
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Smartcat\ConnectorAPI\API\Model\FileInfoViewModel|\Smartcat\ConnectorAPI\API\Model\BadRequestBodyModel|\Smartcat\ConnectorAPI\API\Model\ErrorModel
     */
    public function fileGetFileInfo($id, $token, $parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/file/{id}/{token}/info';
        $url = str_replace('{id}', urlencode($id), $url);
        $url = str_replace('{token}', urlencode($token), $url);
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $queryParam->buildFormDataString($parameters);
        $request = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\FileInfoViewModel', 'json');
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
    /**
     * 
     *
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Smartcat\ConnectorAPI\API\Model\FormatDescriptorViewModel[]|\Smartcat\ConnectorAPI\API\Model\ErrorModel
     */
    public function fileGetSupportedFileFormats($parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/v0/file/formats';
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters));
        $body = $queryParam->buildFormDataString($parameters);
        $request = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\FormatDescriptorViewModel[]', 'json');
            }
            if ('500' == $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'ABBYY\\CloudAPI\\API\\Model\\ErrorModel', 'json');
            }
        }
        return $response;
    }
}