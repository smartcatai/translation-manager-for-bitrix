<?php

namespace Smartcat\ConnectorAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class GetOrdersPageModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\GetOrdersPageModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Smartcat\ConnectorAPI\API\Model\GetOrdersPageModel) {
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
        $object = new \Smartcat\ConnectorAPI\API\Model\GetOrdersPageModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'skip')) {
            $object->setSkip($data->{'skip'});
        }
        if (property_exists($data, 'take')) {
            $object->setTake($data->{'take'});
        }
        if (property_exists($data, 'type')) {
            $object->setType($data->{'type'});
        }
        if (property_exists($data, 'status')) {
            $object->setStatus($data->{'status'});
        }
        if (property_exists($data, 'order_ids')) {
            $values = array();
            foreach ($data->{'order_ids'} as $value) {
                $values[] = $value;
            }
            $object->setOrderIds($values);
        }
        if (property_exists($data, 'is_deleted')) {
            $object->setIsDeleted($data->{'is_deleted'});
        }
        if (property_exists($data, 'email')) {
            $object->setEmail($data->{'email'});
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getSkip()) {
            $data->{'skip'} = $object->getSkip();
        }
        if (null !== $object->getTake()) {
            $data->{'take'} = $object->getTake();
        }
        if (null !== $object->getType()) {
            $data->{'type'} = $object->getType();
        }
        if (null !== $object->getStatus()) {
            $data->{'status'} = $object->getStatus();
        }
        if (null !== $object->getOrderIds()) {
            $values = array();
            foreach ($object->getOrderIds() as $value) {
                $values[] = $value;
            }
            $data->{'order_ids'} = $values;
        }
        if (null !== $object->getIsDeleted()) {
            $data->{'is_deleted'} = $object->getIsDeleted();
        }
        if (null !== $object->getEmail()) {
            $data->{'email'} = $object->getEmail();
        }
        return $data;
    }
}