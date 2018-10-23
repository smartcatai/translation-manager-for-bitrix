<?php

namespace ABBYY\CloudAPI\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
class FileInfoViewModelNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'ABBYY\\CloudAPI\\API\\Model\\FileInfoViewModel') {
            return false;
        }
        return true;
    }
    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \ABBYY\CloudAPI\API\Model\FileInfoViewModel) {
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
        $object = new \ABBYY\CloudAPI\API\Model\FileInfoViewModel();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'id')) {
            $object->setId($data->{'id'});
        }
        if (property_exists($data, 'token')) {
            $object->setToken($data->{'token'});
        }
        if (property_exists($data, 'name')) {
            $object->setName($data->{'name'});
        }
        if (property_exists($data, 'mime')) {
            $object->setMime($data->{'mime'});
        }
        if (property_exists($data, 'is_recognizable')) {
            $object->setIsRecognizable($data->{'is_recognizable'});
        }
        if (property_exists($data, 'expected_languages')) {
            $values = array();
            foreach ($data->{'expected_languages'} as $value) {
                $values[] = $value;
            }
            $object->setExpectedLanguages($values);
        }
        if (property_exists($data, 'ocr_settings')) {
            $object->setOcrSettings($this->serializer->deserialize($data->{'ocr_settings'}, 'ABBYY\\CloudAPI\\API\\Model\\OcrSettingsViewModel', 'raw', $context));
        }
        if (property_exists($data, 'statistics')) {
            $object->setStatistics($this->serializer->deserialize($data->{'statistics'}, 'ABBYY\\CloudAPI\\API\\Model\\TextStatisticsViewModel', 'raw', $context));
        }
        if (property_exists($data, 'ocr_statistics')) {
            $object->setOcrStatistics($this->serializer->deserialize($data->{'ocr_statistics'}, 'ABBYY\\CloudAPI\\API\\Model\\OcrStatisticsViewModel', 'raw', $context));
        }
        if (property_exists($data, 'created')) {
            $object->setCreated(New \DateTime($data->{'created'}));
        }
        if (property_exists($data, 'processed')) {
            $object->setProcessed(New \DateTime($data->{'processed'}));
        }
        if (property_exists($data, 'deleted')) {
            $object->setDeleted(New \DateTime($data->{'deleted'}));
        }
        if (property_exists($data, 'reading_progress')) {
            $object->setReadingProgress($data->{'reading_progress'});
        }
        if (property_exists($data, 'reading_status')) {
            $object->setReadingStatus($data->{'reading_status'});
        }
        if (property_exists($data, 'ocr_warnings')) {
            $values_1 = array();
            foreach ($data->{'ocr_warnings'} as $value_1) {
                $values_1[] = $this->serializer->deserialize($value_1, 'ABBYY\\CloudAPI\\API\\Model\\OcrWarningViewModel', 'raw', $context);
            }
            $object->setOcrWarnings($values_1);
        }
        if (property_exists($data, 'error')) {
            $object->setError($data->{'error'});
        }
        if (property_exists($data, 'is_deleted')) {
            $object->setIsDeleted($data->{'is_deleted'});
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = array())
    {
        $data = new \stdClass();
        if (null !== $object->getId()) {
            $data->{'id'} = $object->getId();
        }
        if (null !== $object->getToken()) {
            $data->{'token'} = $object->getToken();
        }
        if (null !== $object->getName()) {
            $data->{'name'} = $object->getName();
        }
        if (null !== $object->getMime()) {
            $data->{'mime'} = $object->getMime();
        }
        if (null !== $object->getIsRecognizable()) {
            $data->{'is_recognizable'} = $object->getIsRecognizable();
        }
        if (null !== $object->getExpectedLanguages()) {
            $values = array();
            foreach ($object->getExpectedLanguages() as $value) {
                $values[] = $value;
            }
            $data->{'expected_languages'} = $values;
        }
        if (null !== $object->getOcrSettings()) {
            $data->{'ocr_settings'} = $this->serializer->serialize($object->getOcrSettings(), 'raw', $context);
        }
        if (null !== $object->getStatistics()) {
            $data->{'statistics'} = $this->serializer->serialize($object->getStatistics(), 'raw', $context);
        }
        if (null !== $object->getOcrStatistics()) {
            $data->{'ocr_statistics'} = $this->serializer->serialize($object->getOcrStatistics(), 'raw', $context);
        }
        if (null !== $object->getCreated()) {
            $data->{'created'} = $object->getCreated()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getProcessed()) {
            $data->{'processed'} = $object->getProcessed()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getDeleted()) {
            $data->{'deleted'} = $object->getDeleted()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getReadingProgress()) {
            $data->{'reading_progress'} = $object->getReadingProgress();
        }
        if (null !== $object->getReadingStatus()) {
            $data->{'reading_status'} = $object->getReadingStatus();
        }
        if (null !== $object->getOcrWarnings()) {
            $values_1 = array();
            foreach ($object->getOcrWarnings() as $value_1) {
                $values_1[] = $this->serializer->serialize($value_1, 'raw', $context);
            }
            $data->{'ocr_warnings'} = $values_1;
        }
        if (null !== $object->getError()) {
            $data->{'error'} = $object->getError();
        }
        if (null !== $object->getIsDeleted()) {
            $data->{'is_deleted'} = $object->getIsDeleted();
        }
        return $data;
    }
}