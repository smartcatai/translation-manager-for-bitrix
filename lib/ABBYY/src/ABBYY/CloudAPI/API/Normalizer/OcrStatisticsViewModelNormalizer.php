<?php

namespace Smartcat\ConnectorAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class OcrStatisticsViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\OcrStatisticsViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Smartcat\ConnectorAPI\API\Model\OcrStatisticsViewModel) {
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
        $object = new \Smartcat\ConnectorAPI\API\Model\OcrStatisticsViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'exported_pages')) {
            $object->setExportedPages($data->{'exported_pages'});
        }
        if (property_exists($data, 'total_characters')) {
            $object->setTotalCharacters($data->{'total_characters'});
        }
        if (property_exists($data, 'uncertain_characters')) {
            $object->setUncertainCharacters($data->{'uncertain_characters'});
        }
        if (property_exists($data, 'success_part')) {
            $object->setSuccessPart($data->{'success_part'});
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getExportedPages()) {
            $data->{'exported_pages'} = $object->getExportedPages();
        }
        if (null !== $object->getTotalCharacters()) {
            $data->{'total_characters'} = $object->getTotalCharacters();
        }
        if (null !== $object->getUncertainCharacters()) {
            $data->{'uncertain_characters'} = $object->getUncertainCharacters();
        }
        if (null !== $object->getSuccessPart()) {
            $data->{'success_part'} = $object->getSuccessPart();
        }
        return $data;
    }
}