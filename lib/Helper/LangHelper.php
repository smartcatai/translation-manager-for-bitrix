<?php

namespace Smartcat\Connector\Helper;

use Smartcat\ConnectorAPI\SmartCAT;
use Bitrix\Main\Config\Option;

class LangHelper
{

    public static function getLanguages()
    {
        return [
            "ab" => "Abkhaz",
            "aa" => "Afar",
            "af" => "Afrikaans",
            "ak" => "Akan",
            "sq" => "Albanian",
            "am" => "Amharic",
            "ar" => "Arabic",
            "ar-BH" => "Arabic (Bahrain)",
            "ar-EG" => "Arabic (Egypt)",
            "ar-IQ" => "Arabic (Iraq)",
            "ar-JO" => "Arabic (Jordan)",
            "ar-KW" => "Arabic (Kuwait)",
            "ar-LB" => "Arabic (Lebanon)",
            "ar-MA" => "Arabic (Morocco)",
            "ar-QA" => "Arabic (Qatar)",
            "ar-SA" => "Arabic (Saudi Arabia)",
            "ar-AE" => "Arabic (UAE)",
            "hy" => "Armenian",
            "hy-arevela" => "Armenian (Eastern)",
            "hy-arevmda" => "Armenian (Western)",
            "as" => "Assamese",
            "av" => "Avar",
            "az-Cyrl" => "Azerbaijani (Cyrillic)",
            "az-Latn" => "Azerbaijani (Latin)",
            "bcc" => "Balochi (Southern)",
            "bm" => "Bambara",
            "ba" => "Bashkir",
            "eu" => "Basque",
            "be" => "Belarusian",
            "bn" => "Bengali",
            "bh" => "Bihari",
            "bs" => "Bosnian",
            "bg" => "Bulgarian",
            "my" => "Burmese",
            "ca" => "Catalan",
            "ce" => "Chechen",
            "yue" => "Chinese (Cantonese)",
            "zh-Hant-HK" => "Chinese (Hong Kong)",
            "zh-Hant-MO" => "Chinese (Macau)",
            "zh-Hans-MY" => "Chinese (Malaysia)",
            "zh-Hans" => "Chinese (PRC)",
            "zh-Hans-SG" => "Chinese (Singapore)",
            "zh-Hant-TW" => "Chinese (Taiwan)",
            "cv" => "Chuvash",
            "kw" => "Cornish",
            "hr" => "Croatian",
            "cs" => "Czech",
            "da" => "Danish",
            "nl" => "Dutch",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-GB" => "English (United Kingdom)",
            "en-US" => "English (USA)",
            "eo" => "Esperanto",
            "et" => "Estonian",
            "fa" => "Farsi",
            "fil" => "Filipino",
            "fi" => "Finnish",
            "fr" => "French",
            "fr-CA" => "French (Canada)",
            "fr-FR" => "French (France)",
            "fr-CH" => "French (Switzerland)",
            "ff" => "Fula",
            "gl" => "Galician",
            "ka" => "Georgian",
            "de" => "German",
            "de-AT" => "German (Austria)",
            "de-DE" => "German (Germany)",
            "de-CH" => "German (Switzerland)",
            "el" => "Greek",
            "gn" => "Guarani",
            "gu" => "Gujarati",
            "ht" => "Haitian Creole",
            "ha-Latn" => "Hausa (Latin)",
            "haz" => "Hazaragi",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "hmn" => "Hmong",
            "hu" => "Hungarian",
            "is" => "Icelandic",
            "id" => "Indonesian",
            "ga" => "Irish",
            "it" => "Italian",
            "it-IT" => "Italian (Italy)",
            "it-CH" => "Italian (Switzerland)",
            "ja" => "Japanese",
            "jv" => "Javanese",
            "kab" => "Kabyle",
            "kn" => "Kannada",
            "kk" => "Kazakh",
            "km" => "Khmer",
            "rw" => "Kinyarwanda",
            "rn" => "Kirundi",
            "kv" => "Komi",
            "ko" => "Korean",
            "kmr-Latn" => "Kurdish (Kurmandji)",
            "sdh-Arab" => "Kurdish (Palewani)",
            "ckb-Arab" => "Kurdish (Sorani)",
            "ky" => "Kyrgyz",
            "lo" => "Lao",
            "la" => "Latin",
            "lv" => "Latvian",
            "li" => "Limburgish",
            "ln" => "Lingala",
            "lt" => "Lithuanian",
            "lb" => "Luxembourgish",
            "mk" => "Macedonian",
            "mg" => "Malagasy",
            "ms" => "Malay",
            "ms-MY" => "Malay (Malaysia)",
            "ms-SG" => "Malay (Singapore)",
            "ml" => "Malayalam",
            "mi" => "Maori",
            "mr" => "Marathi (Marāṭhī)",
            "mhr" => "Mari",
            "mn" => "Mongolian",
            "ne" => "Nepali",
            "no" => "Norwegian",
            "nb" => "Norwegian (Bokmål)",
            "nn" => "Norwegian (Nynorsk)",
            "oc" => "Occitan",
            "or" => "Odia",
            "os" => "Ossetian",
            "ps" => "Pashto",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "pt-BR" => "Portuguese (Brazil)",
            "pt-PT" => "Portuguese (Portugal)",
            "pa" => "Punjabi",
            "rhg-Latn" => "Rohingya (Latin)",
            "ro" => "Romanian",
            "ro-MD" => "Romanian (Moldova)",
            "ro-RO" => "Romanian (Romania)",
            "ru" => "Russian",
            "sah" => "Sakha",
            "sm" => "Samoan",
            "sg" => "Sango",
            "sa" => "Sanskrit",
            "sc" => "Sardinian",
            "sr-Cyrl" => "Serbian (Cyrillic)",
            "sr-Latn" => "Serbian (Latin)",
            "sn" => "Shona",
            "sd" => "Sindhi",
            "si" => "Sinhalese",
            "sk" => "Slovak",
            "sl" => "Slovenian",
            "so" => "Somali",
            "es" => "Spanish",
            "es-AR" => "Spanish (Argentina)",
            "es-MX" => "Spanish (Mexico)",
            "es-ES" => "Spanish (Spain)",
            "su" => "Sundanese",
            "sw" => "Swahili",
            "sv" => "Swedish",
            "tl" => "Tagalog",
            "tg" => "Tajik",
            "ta" => "Tamil",
            "tt" => "Tatar",
            "te" => "Telugu",
            "th" => "Thai",
            "bo" => "Tibetan",
            "ti" => "Tigrinya",
            "to" => "Tongan",
            "tn" => "Tswana",
            "tr" => "Turkish",
            "tk" => "Turkmen",
            "udm" => "Udmurt",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "ug" => "Uyghur",
            "uz-Latn" => "Uzbek",
            "vi" => "Vietnamese",
            "wo" => "Wolof",
            "yi" => "Yiddish",
            "yo" => "Yoruba",
            "zu" => "Zulu",
        ];
    }

    /**
     * 
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
