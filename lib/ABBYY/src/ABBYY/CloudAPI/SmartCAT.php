<?php
/**
 * Created by PhpStorm.
 * User: Diversant_
 * Date: 20.05.2016
 * Time: 10:36
 */

namespace ABBYY\CloudAPI;

use ABBYY\CloudAPI\API\Normalizer\NormalizerFactory;
use ABBYY\CloudAPI\Manager\FastMTManager;
use ABBYY\CloudAPI\Manager\FileManager;
use ABBYY\CloudAPI\Manager\OrderManager;
use ABBYY\CloudAPI\Manager\PricesManager;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Http\Message\Authentication\BasicAuth;
use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\ContentLengthPlugin;
use Http\Client\Common\Plugin\DecoderPlugin;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Socket\Client as SocketHttpClient;
use Joli\Jane\Encoder\RawEncoder;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class SmartCAT
{
    /**
     * @var HttpClient
     */
    private $httpClient;
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $login логин
     * @param string $password пароль
     */
    public function __construct($login, $password)
    {
        $this->login=$login;
        $this->password=$password;
        $serializer = new Serializer(
            NormalizerFactory::create(),
            [
                new JsonEncoder(
                    new JsonEncode(),
                    new JsonDecode()
                ),
                new RawEncoder()
            ]
        );
        $messageFactory = new MessageFactory\GuzzleMessageFactory();
        $this->serializer = $serializer;
        $this->messageFactory = $messageFactory;
        $options = [
            'remote_socket' => 'tcp://api.perevedem.ru:443',
            'ssl' => true
        ];

        $socketClient = new SocketHttpClient($messageFactory, $options);
        $lengthPlugin = new ContentLengthPlugin();
        $decodingPlugin = new DecoderPlugin();
        $errorPlugin = new ErrorPlugin();
        $authentication = new BasicAuth($this->login, $this->password);
        $authenticationPlugin = new AuthenticationPlugin($authentication);
        $this->httpClient = new PluginClient($socketClient, [
            $errorPlugin,
            $lengthPlugin,
            $decodingPlugin,
            $authenticationPlugin
        ]);
    }


    /**
     * @var FileManager
     */
    private $fileManager;

    /**
     * Интерфейс для работы с файлами
     *
     * @return FileManager
     */
    public function getFileManager()
    {
        if (null === $this->fileManager) {
            $this->fileManager = new FileManager($this->httpClient, $this->messageFactory, $this->serializer);
        }
        return $this->fileManager;
    }

    /**
     * @var FastMTManager
     */
    private $fastMTManager;

    /**
     * Интерфейс для работы с машинным переводом
     *
     * @return FastMTManager
     */
    public function getFastMTManager()
    {
        if (null === $this->fastMTManager) {
            $this->fastMTManager = new FastMTManager($this->httpClient, $this->messageFactory, $this->serializer);
        }
        return $this->fastMTManager;
    }

    /**
     * @var OrderManager
     */
    private $orderManager;

    /**
     * Интерфейс для работы с экспортом документов
     *
     * @return OrderManager
     */
    public function getOrderManager()
    {
        if (null === $this->orderManager) {
            $this->orderManager = new OrderManager($this->httpClient, $this->messageFactory, $this->serializer);
        }
        return $this->orderManager;
    }

    /**
     * @var PricesManager
     */
    private $pricesManager;

    /**
     * Интерфейс для работы с документами
     *
     * @return PricesManager
     */
    public function getPricesManager()
    {
        if (null === $this->pricesManager) {
            $this->pricesManager = new PricesManager($this->httpClient, $this->messageFactory, $this->serializer);
        }
        return $this->pricesManager;
    }

}