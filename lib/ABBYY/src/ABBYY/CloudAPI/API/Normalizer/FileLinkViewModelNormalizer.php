<?php

namespace Smartcat\ConnectorAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class FileLinkViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\FileLinkViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Smartcat\ConnectorAPI\API\Model\FileLinkViewModel) {
            return true;
        }
        return false;
    }
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (empty($data)) {
            return null;
        }
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Smartcat\ConnectorAPI\API\Model\FileLinkViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'id')) {
            $object->setId($data->{'id'});
        }
        if (property_exists($data, 'token')) {
            $object->setToken($data->{'token'});
        }
        if (property_exists($data, 'name')) {
            $object->setName($data->{'name'});
        }
        if (property_exists($data, 'language')) {
            $object->setLanguage($data->{'language'});
        }
        if (property_exists($data, 'chars_count')) {
            $object->setCharsCount($data->{'chars_count'});
        }
        if (property_exists($data, 'words_count')) {
            $object->setWordsCount($data->{'words_count'});
        }
        if (property_exists($data, 'pages_count')) {
            $object->setPagesCount($data->{'pages_count'});
        }
        if (property_exists($data, 'is_deleted')) {
            $object->setIsDeleted($data->{'is_deleted'});
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getId()) {
            $data->{'id'} = $object->getId();
        }
        if (null !== $object->getToken()) {
            $data->{'token'} = $object->getToken();
        }
        if (null !== $object->getName()) {
            $data->{'name'} = $object->getName();
        }
        if (null !== $object->getLanguage()) {
            $data->{'language'} = $object->getLanguage();
        }
        if (null !== $object->getCharsCount()) {
            $data->{'chars_count'} = $object->getCharsCount();
        }
        if (null !== $object->getWordsCount()) {
            $data->{'words_count'} = $object->getWordsCount();
        }
        if (null !== $object->getPagesCount()) {
            $data->{'pages_count'} = $object->getPagesCount();
        }
        if (null !== $object->getIsDeleted()) {
            $data->{'is_deleted'} = $object->getIsDeleted();
        }
        return $data;
    }
}