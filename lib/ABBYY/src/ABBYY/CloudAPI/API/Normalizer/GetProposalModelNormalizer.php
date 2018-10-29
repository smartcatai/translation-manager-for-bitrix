<?php

namespace Smartcat\ConnectorAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class GetProposalModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\GetProposalModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Smartcat\ConnectorAPI\API\Model\GetProposalModel) {
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
        $object = new \Smartcat\ConnectorAPI\API\Model\GetProposalModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'cost_type')) {
            $object->setCostType($data->{'cost_type'});
        }
        if (property_exists($data, 'unit_type')) {
            $object->setUnitType($data->{'unit_type'});
        }
        if (property_exists($data, 'currency')) {
            $object->setCurrency($data->{'currency'});
        }
        if (property_exists($data, 'from')) {
            $object->setFrom($data->{'from'});
        }
        if (property_exists($data, 'to')) {
            $values = array();
            foreach ($data->{'to'} as $value) {
                $values[] = $value;
            }
            $object->setTo($values);
        }
        if (property_exists($data, 'files')) {
            $values_1 = array();
            foreach ($data->{'files'} as $value_1) {
                $values_1[] = $this->serializer->deserialize($value_1, 'ABBYY\\CloudAPI\\API\\Model\\GetFileModel', 'raw', $context);
            }
            $object->setFiles($values_1);
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getCostType()) {
            $data->{'cost_type'} = $object->getCostType();
        }
        if (null !== $object->getUnitType()) {
            $data->{'unit_type'} = $object->getUnitType();
        }
        if (null !== $object->getCurrency()) {
            $data->{'currency'} = $object->getCurrency();
        }
        if (null !== $object->getFrom()) {
            $data->{'from'} = $object->getFrom();
        }
        if (null !== $object->getTo()) {
            $values = array();
            foreach ($object->getTo() as $value) {
                $values[] = $value;
            }
            $data->{'to'} = $values;
        }
        if (null !== $object->getFiles()) {
            $values_1 = array();
            foreach ($object->getFiles() as $value_1) {
                $values_1[] = $this->serializer->serialize($value_1, 'raw', $context);
            }
            $data->{'files'} = $values_1;
        }
        return $data;
    }
}