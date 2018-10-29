<?php

namespace Smartcat\ConnectorAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class QuoteViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\QuoteViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Smartcat\ConnectorAPI\API\Model\QuoteViewModel) {
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
        $object = new \Smartcat\ConnectorAPI\API\Model\QuoteViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'file_id')) {
            $object->setFileId($data->{'file_id'});
        }
        if (property_exists($data, 'from')) {
            $object->setFrom($data->{'from'});
        }
        if (property_exists($data, 'to')) {
            $object->setTo($data->{'to'});
        }
        if (property_exists($data, 'unit_count')) {
            $object->setUnitCount($data->{'unit_count'});
        }
        if (property_exists($data, 'cost_per_unit')) {
            $object->setCostPerUnit($data->{'cost_per_unit'});
        }
        if (property_exists($data, 'amount')) {
            $object->setAmount($data->{'amount'});
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getFileId()) {
            $data->{'file_id'} = $object->getFileId();
        }
        if (null !== $object->getFrom()) {
            $data->{'from'} = $object->getFrom();
        }
        if (null !== $object->getTo()) {
            $data->{'to'} = $object->getTo();
        }
        if (null !== $object->getUnitCount()) {
            $data->{'unit_count'} = $object->getUnitCount();
        }
        if (null !== $object->getCostPerUnit()) {
            $data->{'cost_per_unit'} = $object->getCostPerUnit();
        }
        if (null !== $object->getAmount()) {
            $data->{'amount'} = $object->getAmount();
        }
        return $data;
    }
}