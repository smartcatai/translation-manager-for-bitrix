<?php

namespace Smartcat\ConnectorAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class ProposalViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\ProposalViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Smartcat\ConnectorAPI\API\Model\ProposalViewModel) {
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
        $object = new \Smartcat\ConnectorAPI\API\Model\ProposalViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'type')) {
            $object->setType($data->{'type'});
        }
        if (property_exists($data, 'lead_time')) {
            $object->setLeadTime($data->{'lead_time'});
        }
        if (property_exists($data, 'unit_type')) {
            $object->setUnitType($data->{'unit_type'});
        }
        if (property_exists($data, 'unit_count')) {
            $object->setUnitCount($data->{'unit_count'});
        }
        if (property_exists($data, 'currency')) {
            $object->setCurrency($data->{'currency'});
        }
        if (property_exists($data, 'amount')) {
            $object->setAmount($data->{'amount'});
        }
        if (property_exists($data, 'quotes')) {
            $values = array();
            foreach ($data->{'quotes'} as $value) {
                $values[] = $this->serializer->deserialize($value, 'ABBYY\\CloudAPI\\API\\Model\\QuoteViewModel', 'raw', $context);
            }
            $object->setQuotes($values);
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getType()) {
            $data->{'type'} = $object->getType();
        }
        if (null !== $object->getLeadTime()) {
            $data->{'lead_time'} = $object->getLeadTime();
        }
        if (null !== $object->getUnitType()) {
            $data->{'unit_type'} = $object->getUnitType();
        }
        if (null !== $object->getUnitCount()) {
            $data->{'unit_count'} = $object->getUnitCount();
        }
        if (null !== $object->getCurrency()) {
            $data->{'currency'} = $object->getCurrency();
        }
        if (null !== $object->getAmount()) {
            $data->{'amount'} = $object->getAmount();
        }
        if (null !== $object->getQuotes()) {
            $values = array();
            foreach ($object->getQuotes() as $value) {
                $values[] = $this->serializer->serialize($value, 'raw', $context);
            }
            $data->{'quotes'} = $values;
        }
        return $data;
    }
}