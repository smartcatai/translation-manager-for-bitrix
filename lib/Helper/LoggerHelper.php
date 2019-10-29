<?php

namespace Smartcat\Connector\Helper;

class LoggerHelper
{
    const SEVERITY_ERROR = 'ERROR';
    const SEVERITY_SECURITY = 'SECURITY';
    const SEVERITY_WARNING = 'WARNING';
    const SEVERITY_INFO = 'INFO';
    const SEVERITY_DEBUG = 'DEBUG';

    public static function addRecord($severity, $eventId, $message, $itemId = null) {
        $data = array(
            'SEVERITY' => $severity,
            'AUDIT_TYPE_ID' => $eventId,
            'MODULE_ID' => 'smartcat.connector',
            'DESCRIPTION' => $message
        );

        if ($itemId) {
            $data['ITEM_ID'] = $itemId;
        } else {
            $data['ITEM_ID'] = '-';
        }

        return \CEventLog::Add($data);
    }

    public static function error($eventId, $message, $itemId = null) {
        return self::addRecord(self::SEVERITY_ERROR, $eventId, $message, $itemId);
    }

    public static function warning($eventId, $message, $itemId = null) {
        return self::addRecord(self::SEVERITY_WARNING, $eventId, $message, $itemId);
    }

    public static function info($eventId, $message, $itemId = null) {
        return self::addRecord(self::SEVERITY_INFO, $eventId, $message, $itemId);
    }

    public static function debug($eventId, $message, $itemId = null) {
        return self::addRecord(self::SEVERITY_DEBUG, $eventId, $message, $itemId);
    }
}