<?php

namespace Smartcat\ConnectorAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class ServicePriceViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\ServicePriceViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Smartcat\ConnectorAPI\API\Model\ServicePriceViewModel) {
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
        $object = new \Smartcat\ConnectorAPI\API\Model\ServicePriceViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'id')) {
            $object->setId($data->{'id'});
        }
        if (property_exists($data, 'account_id')) {
            $object->setAccountId($data->{'account_id'});
        }
        if (property_exists($data, 'type')) {
            $object->setType($data->{'type'});
        }
        if (property_exists($data, 'from')) {
            $object->setFrom($data->{'from'});
        }
        if (property_exists($data, 'to')) {
            $object->setTo($data->{'to'});
        }
        if (property_exists($data, 'unit_prices')) {
            $values = array();
            foreach ($data->{'unit_prices'} as $value) {
                $values[] = $this->serializer->deserialize($value, 'ABBYY\\CloudAPI\\API\\Model\\UnitPriceViewModel', 'raw', $context);
            }
            $object->setUnitPrices($values);
        }
        if (property_exists($data, 'discounts')) {
            $values_1 = array();
            foreach ($data->{'discounts'} as $value_1) {
                $values_1[] = $this->serializer->deserialize($value_1, 'ABBYY\\CloudAPI\\API\\Model\\DiscountViewModel', 'raw', $context);
            }
            $object->setDiscounts($values_1);
        }
        if (property_exists($data, 'created')) {
            $object->setCreated(New \DateTime($data->{'created'}));
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getId()) {
            $data->{'id'} = $object->getId();
        }
        if (null !== $object->getAccountId()) {
            $data->{'account_id'} = $object->getAccountId();
        }
        if (null !== $object->getType()) {
            $data->{'type'} = $object->getType();
        }
        if (null !== $object->getFrom()) {
            $data->{'from'} = $object->getFrom();
        }
        if (null !== $object->getTo()) {
            $data->{'to'} = $object->getTo();
        }
        if (null !== $object->getUnitPrices()) {
            $values = array();
            foreach ($object->getUnitPrices() as $value) {
                $values[] = $this->serializer->serialize($value, 'raw', $context);
            }
            $data->{'unit_prices'} = $values;
        }
        if (null !== $object->getDiscounts()) {
            $values_1 = array();
            foreach ($object->getDiscounts() as $value_1) {
                $values_1[] = $this->serializer->serialize($value_1, 'raw', $context);
            }
            $data->{'discounts'} = $values_1;
        }
        if (null !== $object->getCreated()) {
            $data->{'created'} = $object->getCreated()->format("Y-m-d\TH:i:sP");
        }
        return $data;
    }
}