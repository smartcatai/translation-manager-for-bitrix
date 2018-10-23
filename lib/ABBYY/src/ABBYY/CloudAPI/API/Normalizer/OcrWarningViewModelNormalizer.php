<?php

namespace ABBYY\CloudAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class OcrWarningViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\OcrWarningViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \ABBYY\CloudAPI\API\Model\OcrWarningViewModel) {
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
        $object = new \ABBYY\CloudAPI\API\Model\OcrWarningViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'warning_type')) {
            $object->setWarningType($data->{'warning_type'});
        }
        if (property_exists($data, 'page_number')) {
            $object->setPageNumber($data->{'page_number'});
        }
        if (property_exists($data, 'dpi')) {
            $object->setDpi($data->{'dpi'});
        }
        if (property_exists($data, 'language_count')) {
            $object->setLanguageCount($data->{'language_count'});
        }
        if (property_exists($data, 'full_warning_message')) {
            $object->setFullWarningMessage($data->{'full_warning_message'});
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getWarningType()) {
            $data->{'warning_type'} = $object->getWarningType();
        }
        if (null !== $object->getPageNumber()) {
            $data->{'page_number'} = $object->getPageNumber();
        }
        if (null !== $object->getDpi()) {
            $data->{'dpi'} = $object->getDpi();
        }
        if (null !== $object->getLanguageCount()) {
            $data->{'language_count'} = $object->getLanguageCount();
        }
        if (null !== $object->getFullWarningMessage()) {
            $data->{'full_warning_message'} = $object->getFullWarningMessage();
        }
        return $data;
    }
}