<?php
/**
 * Created by PhpStorm.
 * User: Diversant_
 * Date: 20.05.2016
 * Time: 14:34
 */

namespace ABBYY\CloudAPI\Manager;
use ABBYY\CloudAPI\API\Resource\FileResource;
use Joli\Jane\OpenApi\Client\QueryParam;

class FileManager extends FileResource
{
    use CommonManager;
    /**
     * <b>Supported formats:</b> <br/> .html; .htm; .inx; .json; .pdf; .bmp; .dcx; .pcx; .png; .jp2; .jpc; .jpg; .jpeg; .jfif; .tif; .tiff; .gif; .djvu; .djv; .jb2; .php; .inc; .po; .resx; .zip; .docx; .doc; .txt; .rtf; .odt; .ppt; .pptx; .potx; .pps; .ppsx; .odp; .srt; .xlsx; .xls; .tjson; .ttx; .sdlxliff; .xlf; .xliff; .xml
     *
     * @param array  $parameters {
     *     @var string $exportFormat
     *     @var string $quality
     *     @var string $synthesisMode
     *     @var array $languages
     *     @var string $filePath path to the file
     *     @var string $fileName file name
     * }
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\ABBYY\CloudAPI\API\Model\FileInfoViewModel[]|\ABBYY\CloudAPI\API\Model\ErrorModel
     */
    public function fileUploadFile($parameters = array(), $fetch = self::FETCH_OBJECT)
    {
        $formParams=[];
        $files['file']=[];
        $files['file']['filename']=$parameters['fileName'];
        $files['file']['content']=file_get_contents($parameters['filePath']);
        $form_data=$this->createFormData($formParams, $files, ['Accept'=> 'application/json']);
        unset($parameters['fileName']);
        unset($parameters['filePath']);

        $queryParam = new QueryParam();
        $queryParam->setDefault('exportFormat', NULL);
        $queryParam->setDefault('quality', NULL);
        $queryParam->setDefault('synthesisMode', NULL);
        $queryParam->setDefault('languages', NULL);
        $url = '/v0/file';
        $url = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers = array_merge(array('Host' => 'api.perevedem.ru'), $queryParam->buildHeaders($parameters), $form_data['headers']);

        $request = $this->messageFactory->createRequest('POST', $url, $headers, $form_data['body']);
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
}