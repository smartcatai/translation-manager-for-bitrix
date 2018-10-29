<?php

namespace Smartcat\Connector\Helper;

use Smartcat\ConnectorAPI\SmartCAT;
use Bitrix\Main\Config\Option;

class LangHelper
{

    public static function getLanguages()
    {
        return [
            "af" => "Afrikaans",
            "af-ZA" => "Afrikaans (South Africa)",
            "ar" => "Arabic",
            "ar-AE" => "Arabic (U.A.E.)",
            "ar-BH" => "Arabic (Bahrain)",
            "ar-DZ" => "Arabic (Algeria)",
            "ar-EG" => "Arabic (Egypt)",
            "ar-IQ" => "Arabic (Iraq)",
            "ar-JO" => "Arabic (Jordan)",
            "ar-KW" => "Arabic (Kuwait)",
            "ar-LB" => "Arabic (Lebanon)",
            "ar-LY" => "Arabic (Libya)",
            "ar-MA" => "Arabic (Morocco)",
            "ar-OM" => "Arabic (Oman)",
            "ar-QA" => "Arabic (Qatar)",
            "ar-SA" => "Arabic (Saudi Arabia)",
            "ar-SY" => "Arabic (Syria)",
            "ar-TN" => "Arabic (Tunisia)",
            "ar-YE" => "Arabic (Yemen)",
            "az" => "Azeri (Latin)",
            "az-AZ" => "Azeri (Latin, Cyrillic) (Azerbaijan)",
            "be" => "Belarusian",
            "be-BY" => "Belarusian (Belarus)",
            "bg" => "Bulgarian",
            "bg-BG" => "Bulgarian (Bulgaria)",
            "bs-BA" => "Bosnian (Bosnia and Herzegovina)",
            "ca" => "Catalan",
            "ca-ES" => "Catalan (Spain)",
            "cs" => "Czech",
            "cs-CZ" => "Czech (Czech Republic)",
            "cy" => "Welsh",
            "cy-GB" => "Welsh (United Kingdom)",
            "da" => "Danish",
            "da-DK" => "Danish (Denmark)",
            "de" => "German",
            "de-AT" => "German (Austria)",
            "de-CH" => "German (Switzerland)",
            "de-DE" => "German (Germany)",
            "de-LI" => "German (Liechtenstein)",
            "de-LU" => "German (Luxembourg)",
            "dv" => "Divehi",
            "dv-MV" => "Divehi (Maldives)",
            "el" => "Greek",
            "el-GR" => "Greek (Greece)",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-BZ" => "English (Belize)",
            "en-CA" => "English (Canada)",
            "en-CB" => "English (Caribbean)",
            "en-GB" => "English (United Kingdom)",
            "en-IE" => "English (Ireland)",
            "en-JM" => "English (Jamaica)",
            "en-NZ" => "English (New Zealand)",
            "en-PH" => "English (Republic of the Philippines)",
            "en-TT" => "English (Trinidad and Tobago)",
            "en-US" => "English (United States)",
            "en-ZA" => "English (South Africa)",
            "en-ZW" => "English (Zimbabwe)",
            "eo" => "Esperanto",
            "es" => "Spanish",
            "es-AR" => "Spanish (Argentina)",
            "es-BO" => "Spanish (Bolivia)",
            "es-CL" => "Spanish (Chile)",
            "es-CO" => "Spanish (Colombia)",
            "es-CR" => "Spanish (Costa Rica)",
            "es-DO" => "Spanish (Dominican Republic)",
            "es-EC" => "Spanish (Ecuador)",
            "es-ES" => "Spanish (Castilian, Spain)",
            "es-GT" => "Spanish (Guatemala)",
            "es-HN" => "Spanish (Honduras)",
            "es-MX" => "Spanish (Mexico)",
            "es-NI" => "Spanish (Nicaragua)",
            "es-PA" => "Spanish (Panama)",
            "es-PE" => "Spanish (Peru)",
            "es-PR" => "Spanish (Puerto Rico)",
            "es-PY" => "Spanish (Paraguay)",
            "es-SV" => "Spanish (El Salvador)",
            "es-UY" => "Spanish (Uruguay)",
            "es-VE" => "Spanish (Venezuela)",
            "et" => "Estonian",
            "et-EE" => "Estonian (Estonia)",
            "eu" => "Basque",
            "eu-ES" => "Basque (Spain)",
            "fa" => "Farsi",
            "fa-IR" => "Farsi (Iran)",
            "fi" => "Finnish",
            "fi-FI" => "Finnish (Finland)",
            "fo" => "Faroese",
            "fo-FO" => "Faroese (Faroe Islands)",
            "fr" => "French",
            "fr-BE" => "French (Belgium)",
            "fr-CA" => "French (Canada)",
            "fr-CH" => "French (Switzerland)",
            "fr-FR" => "French (France)",
            "fr-LU" => "French (Luxembourg)",
            "fr-MC" => "French (Principality of Monaco)",
            "gl" => "Galician",
            "gl-ES" => "Galician (Spain)",
            "gu" => "Gujarati",
            "gu-IN" => "Gujarati (India)",
            "he" => "Hebrew",
            "he-IL" => "Hebrew (Israel)",
            "hi" => "Hindi",
            "hi-IN" => "Hindi (India)",
            "hr" => "Croatian",
            "hr-BA" => "Croatian (Bosnia and Herzegovina)",
            "hr-HR" => "Croatian (Croatia)",
            "hu" => "Hungarian",
            "hu-HU" => "Hungarian (Hungary)",
            "hy" => "Armenian",
            "hy-AM" => "Armenian (Armenia)",
            "id" => "Indonesian",
            "id-ID" => "Indonesian (Indonesia)",
            "is" => "Icelandic",
            "is-IS" => "Icelandic (Iceland)",
            "it" => "Italian",
            "it-CH" => "Italian (Switzerland)",
            "it-IT" => "Italian (Italy)",
            "ja" => "Japanese",
            "ja-JP" => "Japanese (Japan)",
            "ka" => "Georgian",
            "ka-GE" => "Georgian (Georgia)",
            "kk" => "Kazakh",
            "kk-KZ" => "Kazakh (Kazakhstan)",
            "kn" => "Kannada",
            "kn-IN" => "Kannada (India)",
            "ko" => "Korean",
            "ko-KR" => "Korean (Korea)",
            "kok" => "Konkani",
            "kok-IN" => "Konkani (India)",
            "ky" => "Kyrgyz",
            "ky-KG" => "Kyrgyz (Kyrgyzstan)",
            "lt" => "Lithuanian",
            "lt-LT" => "Lithuanian (Lithuania)",
            "lv" => "Latvian",
            "lv-LV" => "Latvian (Latvia)",
            "mi" => "Maori",
            "mi-NZ" => "Maori (New Zealand)",
            "mk" => "FYRO Macedonian",
            "mk-MK" => "FYRO Macedonian (Former Yugoslav Republic of Macedonia)",
            "mn" => "Mongolian",
            "mn-MN" => "Mongolian (Mongolia)",
            "mr" => "Marathi",
            "mr-IN" => "Marathi (India)",
            "ms" => "Malay",
            "ms-BN" => "Malay (Brunei Darussalam)",
            "ms-MY" => "Malay (Malaysia)",
            "mt" => "Maltese",
            "mt-MT" => "Maltese (Malta)",
            "nb" => "Norwegian (Bokm?l)",
            "nb-NO" => "Norwegian (Bokm?l) (Norway)",
            "nl" => "Dutch",
            "nl-BE" => "Dutch (Belgium)",
            "nl-NL" => "Dutch (Netherlands)",
            "nn-NO" => "Norwegian (Nynorsk) (Norway)",
            "ns" => "Northern Sotho",
            "ns-ZA" => "Northern Sotho (South Africa)",
            "pa" => "Punjabi",
            "pa-IN" => "Punjabi (India)",
            "pl" => "Polish",
            "pl-PL" => "Polish (Poland)",
            "ps" => "Pashto",
            "ps-AR" => "Pashto (Afghanistan)",
            "pt" => "Portuguese",
            "pt-BR" => "Portuguese (Brazil)",
            "pt-PT" => "Portuguese (Portugal)",
            "qu" => "Quechua",
            "qu-BO" => "Quechua (Bolivia)",
            "qu-EC" => "Quechua (Ecuador)",
            "qu-PE" => "Quechua (Peru)",
            "ro" => "Romanian",
            "ro-RO" => "Romanian (Romania)",
            "ru" => "Russian",
            "ru-RU" => "Russian (Russia)",
            "sa" => "Sanskrit",
            "sa-IN" => "Sanskrit (India)",
            "se" => "Sami (Northern)",
            "se-FI" => "Sami (Northern, Skolt, Inari) (Finland)",
            "se-NO" => "Sami (Northern, Lule, Southern) (Norway)",
            "se-SE" => "Sami (Northern, Lule, Southern) (Sweden)",
            "sk" => "Slovak",
            "sk-SK" => "Slovak (Slovakia)",
            "sl" => "Slovenian",
            "sl-SI" => "Slovenian (Slovenia)",
            "sq" => "Albanian",
            "sq-AL" => "Albanian (Albania)",
            "sr-BA" => "Serbian (Latin, Cyrillic) (Bosnia and Herzegovina)",
            "sr-SP" => "Serbian (Latin, Cyrillic) (Serbia and Montenegro)",
            "sv" => "Swedish",
            "sv-FI" => "Swedish (Finland)",
            "sv-SE" => "Swedish (Sweden)",
            "sw" => "Swahili",
            "sw-KE" => "Swahili (Kenya)",
            "syr" => "Syriac",
            "syr-SY" => "Syriac (Syria)",
            "ta" => "Tamil",
            "ta-IN" => "Tamil (India)",
            "te" => "Telugu",
            "te-IN" => "Telugu (India)",
            "th" => "Thai",
            "th-TH" => "Thai (Thailand)",
            "tl" => "Tagalog",
            "tl-PH" => "Tagalog (Philippines)",
            "tn" => "Tswana",
            "tn-ZA" => "Tswana (South Africa)",
            "tr" => "Turkish",
            "tr-TR" => "Turkish (Turkey)",
            "tt" => "Tatar",
            "tt-RU" => "Tatar (Russia)",
            "ts" => "Tsonga",
            "uk" => "Ukrainian",
            "uk-UA" => "Ukrainian (Ukraine)",
            "ur" => "Urdu",
            "ur-PK" => "Urdu (Islamic Republic of Pakistan)",
            "uz" => "Uzbek (Latin)",
            "uz-UZ" => "Uzbek (Latin, Cyrillic) (Uzbekistan)",
            "vi" => "Vietnamese",
            "vi-VN" => "Vietnamese (Viet Nam)",
            "xh" => "Xhosa",
            "xh-ZA" => "Xhosa (South Africa)",
            "zh" => "Chinese",
            "zh-Hans-CN" => "Chinese",
            "zh-CN" => "Chinese (S)",
            "zh-HK" => "Chinese (Hong Kong)",
            "zh-MO" => "Chinese (Macau)",
            "zh-SG" => "Chinese (Singapore)",
            "zh-TW" => "Chinese (T)",
            "zh-Hant-TW" => "Chinese (T)",
            "zu" => "Zulu",
            "zu-ZA" => "Zulu (South Africa)",
        ];
    }

    /**
     * ���������� ������ ��������� ������ ��� ������� ��������
     */
    public static function getLanguagesForHT($langFrom, $htType)
    {
        $cloudApi =  \Smartcat\Connector\Helper\ApiHelper::createApi();

        $priceManager = $cloudApi->getPricesManager();

        $arLanguages = self::getLanguages();
        $arLanguagesTo = [];
        $offset = 0;
        $limit = 100;
        while (true) {
            try {
                $result = $priceManager->pricesGetAccountPrices([
                    'skip' => $offset,
                    'take' => $limit,
                    'from' => $langFrom,
                    'type' => $htType,
                ]);
            } catch (\Exception $e) {
                $arErrors[] = $e->getMessage();
            }

            if (is_array($result)) {
                foreach ($result as $price) {
                    $langTo = $price->getTo();
                    if (array_key_exists($langTo, $arLanguages)) {
                        $arLanguagesTo[$langTo] = $arLanguages[$langTo];
                    }
                }
            }

            if (empty($result) || count($result) < $limit) {
                break;
            }
            $offset += $limit;
        }

        return $arLanguagesTo;
    }
}