<?php

namespace Smartcat\ConnectorAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class OrderViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\OrderViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Smartcat\ConnectorAPI\API\Model\OrderViewModel) {
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
        $object = new \Smartcat\ConnectorAPI\API\Model\OrderViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'id')) {
            $object->setId($data->{'id'});
        }
        if (property_exists($data, 'number')) {
            $object->setNumber($data->{'number'});
        }
        if (property_exists($data, 'email')) {
            $object->setEmail($data->{'email'});
        }
        if (property_exists($data, 'type')) {
            $object->setType($data->{'type'});
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
        if (property_exists($data, 'is_layout_required')) {
            $object->setIsLayoutRequired($data->{'is_layout_required'});
        }
        if (property_exists($data, 'label')) {
            $object->setLabel($data->{'label'});
        }
        if (property_exists($data, 'payment_type')) {
            $object->setPaymentType($data->{'payment_type'});
        }
        if (property_exists($data, 'unit_type')) {
            $object->setUnitType($data->{'unit_type'});
        }
        if (property_exists($data, 'unit_count')) {
            $object->setUnitCount($data->{'unit_count'});
        }
        if (property_exists($data, 'units_count')) {
            $values_1 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'units_count'} as $key => $value_1) {
                $values_1[$key] = $value_1;
            }
            $object->setUnitsCount($values_1);
        }
        if (property_exists($data, 'currency')) {
            $object->setCurrency($data->{'currency'});
        }
        if (property_exists($data, 'amount')) {
            $object->setAmount($data->{'amount'});
        }
        if (property_exists($data, 'deadline')) {
            $object->setDeadline(New \DateTime($data->{'deadline'}));
        }
        if (property_exists($data, 'payment_provider')) {
            $object->setPaymentProvider($data->{'payment_provider'});
        }
        if (property_exists($data, 'created')) {
            $object->setCreated(New \DateTime($data->{'created'}));
        }
        if (property_exists($data, 'started')) {
            $object->setStarted(New \DateTime($data->{'started'}));
        }
        if (property_exists($data, 'delivered')) {
            $object->setDelivered(New \DateTime($data->{'delivered'}));
        }
        if (property_exists($data, 'progress')) {
            $object->setProgress($data->{'progress'});
        }
        if (property_exists($data, 'status')) {
            $object->setStatus($data->{'status'});
        }
        if (property_exists($data, 'approval_required')) {
            $object->setApprovalRequired($data->{'approval_required'});
        }
        if (property_exists($data, 'deleted')) {
            $object->setDeleted(New \DateTime($data->{'deleted'}));
        }
        if (property_exists($data, 'is_deleted')) {
            $object->setIsDeleted($data->{'is_deleted'});
        }
        if (property_exists($data, 'statistics')) {
            $object->setStatistics($this->serializer->deserialize($data->{'statistics'}, 'ABBYY\\CloudAPI\\API\\Model\\OrderStatisticsViewModel', 'raw', $context));
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getId()) {
            $data->{'id'} = $object->getId();
        }
        if (null !== $object->getNumber()) {
            $data->{'number'} = $object->getNumber();
        }
        if (null !== $object->getEmail()) {
            $data->{'email'} = $object->getEmail();
        }
        if (null !== $object->getType()) {
            $data->{'type'} = $object->getType();
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
        if (null !== $object->getIsLayoutRequired()) {
            $data->{'is_layout_required'} = $object->getIsLayoutRequired();
        }
        if (null !== $object->getLabel()) {
            $data->{'label'} = $object->getLabel();
        }
        if (null !== $object->getPaymentType()) {
            $data->{'payment_type'} = $object->getPaymentType();
        }
        if (null !== $object->getUnitType()) {
            $data->{'unit_type'} = $object->getUnitType();
        }
        if (null !== $object->getUnitCount()) {
            $data->{'unit_count'} = $object->getUnitCount();
        }
        if (null !== $object->getUnitsCount()) {
            $values_1 = new \stdClass();
            foreach ($object->getUnitsCount() as $key => $value_1) {
                $values_1->{$key} = $value_1;
            }
            $data->{'units_count'} = $values_1;
        }
        if (null !== $object->getCurrency()) {
            $data->{'currency'} = $object->getCurrency();
        }
        if (null !== $object->getAmount()) {
            $data->{'amount'} = $object->getAmount();
        }
        if (null !== $object->getDeadline()) {
            $data->{'deadline'} = $object->getDeadline()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getPaymentProvider()) {
            $data->{'payment_provider'} = $object->getPaymentProvider();
        }
        if (null !== $object->getCreated()) {
            $data->{'created'} = $object->getCreated()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getStarted()) {
            $data->{'started'} = $object->getStarted()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getDelivered()) {
            $data->{'delivered'} = $object->getDelivered()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getProgress()) {
            $data->{'progress'} = $object->getProgress();
        }
        if (null !== $object->getStatus()) {
            $data->{'status'} = $object->getStatus();
        }
        if (null !== $object->getApprovalRequired()) {
            $data->{'approval_required'} = $object->getApprovalRequired();
        }
        if (null !== $object->getDeleted()) {
            $data->{'deleted'} = $object->getDeleted()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getIsDeleted()) {
            $data->{'is_deleted'} = $object->getIsDeleted();
        }
        if (null !== $object->getStatistics()) {
            $data->{'statistics'} = $this->serializer->serialize($object->getStatistics(), 'raw', $context);
        }
        return $data;
    }
}