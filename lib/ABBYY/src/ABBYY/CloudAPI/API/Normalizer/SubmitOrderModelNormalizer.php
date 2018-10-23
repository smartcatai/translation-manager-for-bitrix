<?php

namespace ABBYY\CloudAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class SubmitOrderModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\SubmitOrderModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \ABBYY\CloudAPI\API\Model\SubmitOrderModel) {
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
        $object = new \ABBYY\CloudAPI\API\Model\SubmitOrderModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'type')) {
            $object->setType($data->{'type'});
        }
        if (property_exists($data, 'email')) {
            $object->setEmail($data->{'email'});
        }
        if (property_exists($data, 'contact_culture')) {
            $object->setContactCulture($data->{'contact_culture'});
        }
        if (property_exists($data, 'contact_utc_offset')) {
            $object->setContactUtcOffset($data->{'contact_utc_offset'});
        }
        if (property_exists($data, 'label')) {
            $object->setLabel($data->{'label'});
        }
        if (property_exists($data, 'approval_required')) {
            $object->setApprovalRequired($data->{'approval_required'});
        }
        if (property_exists($data, 'is_manual_estimation')) {
            $object->setIsManualEstimation($data->{'is_manual_estimation'});
        }
        if (property_exists($data, 'cost_type')) {
            $object->setCostType($data->{'cost_type'});
        }
        if (property_exists($data, 'deadline')) {
            $object->setDeadline($data->{'deadline'});
        }
        if (property_exists($data, 'dead_line')) {
            $object->setDeadline($data->{'dead_line'});
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
        if (null !== $object->getType()) {
            $data->{'type'} = $object->getType();
        }
        if (null !== $object->getEmail()) {
            $data->{'email'} = $object->getEmail();
        }
        if (null !== $object->getContactCulture()) {
            $data->{'contact_culture'} = $object->getContactCulture();
        }
        if (null !== $object->getContactUtcOffset()) {
            $data->{'contact_utc_offset'} = $object->getContactUtcOffset();
        }
        if (null !== $object->getLabel()) {
            $data->{'label'} = $object->getLabel();
        }
        if (null !== $object->getApprovalRequired()) {
            $data->{'approval_required'} = $object->getApprovalRequired();
        }
        if (null !== $object->getIsManualEstimation()) {
            $data->{'is_manual_estimation'} = $object->getIsManualEstimation();
        }
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
        if (null !== $object->getDeadline()) {
            $data->{'deadline'} = $object->getDeadline();
        }
        if (null !== $object->getDeadline()) {
            $data->{'dead_line'} = $object->getDeadline();
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