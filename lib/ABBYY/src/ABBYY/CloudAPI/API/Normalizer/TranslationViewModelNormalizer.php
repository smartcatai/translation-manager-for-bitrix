<?php

namespace ABBYY\CloudAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class TranslationViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\TranslationViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \ABBYY\CloudAPI\API\Model\TranslationViewModel) {
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
        $object = new \ABBYY\CloudAPI\API\Model\TranslationViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'source_file')) {
            $object->setSourceFile($this->serializer->deserialize($data->{'source_file'}, 'ABBYY\\CloudAPI\\API\\Model\\FileLinkViewModel', 'raw', $context));
        }
        if (property_exists($data, 'target_file')) {
            $object->setTargetFile($this->serializer->deserialize($data->{'target_file'}, 'ABBYY\\CloudAPI\\API\\Model\\FileLinkViewModel', 'raw', $context));
        }
        if (property_exists($data, 'started')) {
            $object->setStarted(New \DateTime($data->{'started'}));
        }
        if (property_exists($data, 'delivered')) {
            $object->setDelivered(New \DateTime($data->{'delivered'}));
        }
        if (property_exists($data, 'progress')) {
            $values = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'progress'} as $key => $value) {
                $values[$key] = $value;
            }
            $object->setProgress($values);
        }
        if (property_exists($data, 'status')) {
            $object->setStatus($data->{'status'});
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getSourceFile()) {
            $data->{'source_file'} = $this->serializer->serialize($object->getSourceFile(), 'raw', $context);
        }
        if (null !== $object->getTargetFile()) {
            $data->{'target_file'} = $this->serializer->serialize($object->getTargetFile(), 'raw', $context);
        }
        if (null !== $object->getStarted()) {
            $data->{'started'} = $object->getStarted()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getDelivered()) {
            $data->{'delivered'} = $object->getDelivered()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getProgress()) {
            $values = new \stdClass();
            foreach ($object->getProgress() as $key => $value) {
                $values->{$key} = $value;
            }
            $data->{'progress'} = $values;
        }
        if (null !== $object->getStatus()) {
            $data->{'status'} = $object->getStatus();
        }
        return $data;
    }
}