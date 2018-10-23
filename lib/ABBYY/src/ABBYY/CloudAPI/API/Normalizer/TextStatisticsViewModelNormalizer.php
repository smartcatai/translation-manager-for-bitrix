<?php

namespace ABBYY\CloudAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class TextStatisticsViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\TextStatisticsViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \ABBYY\CloudAPI\API\Model\TextStatisticsViewModel) {
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
        $object = new \ABBYY\CloudAPI\API\Model\TextStatisticsViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
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
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
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
        return $data;
    }
}