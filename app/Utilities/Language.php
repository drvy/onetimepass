<?php

namespace App\Utilities;

use Exception;
use DirectoryIterator;
use Negotiation\LanguageNegotiator;

class Language
{
    /**
     * Retrieve the header for language acceptance
     *
     * @return string
     */
    public static function getAcceptLanguage(): string
    {
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }

        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 80);
    }


    /**
     * Detect the current languages that this application has templates for
     *
     * @return array
     */
    public static function detectLanguages(): array
    {
        try {
            $path      = ABSPATH . '/app/Views/';
            $dir       = new DirectoryIterator($path);
            $languages = array();

            foreach ($dir as $fileInfo) {
                if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                    $languages[] = $fileInfo->getBasename();
                }
            }

            return $languages;
        } catch (Exception $error) {
            return array();
        }
    }


    /**
     * Prioritize a specific language in an array of languages.
     *
     * If no default language is given, and 'en' is in the language list,
     * it will prioritize it.
     *
     * @param string $language
     * @param array $priorties
     * @return array
     */
    public static function prioritize($language, array $languages)
    {
        if (empty($language) && in_array('en', $languages)) {
            $language = 'en';
        }

        usort($languages, function ($a, $b) use ($language) {
            return ($language === $a ? -1 : ($language === $b ? 1 : 0));
        });

        return $languages;
    }


    /**
     * Find the best language available for the current user.
     *
     * @param mixed $default
     * @return string
     * @throws Exception
     */
    public static function getLanguage($default): string
    {
        $acceptLanguageHeader = self::getAcceptLanguage();
        $priorities           = self::prioritize($default, self::detectLanguages());

        if (empty($priorities)) {
            throw new Exception('No language found.');
        }

        $bestFit = (new LanguageNegotiator())->getBest($acceptLanguageHeader, $priorities);
        return ($bestFit ? $bestFit->getType() : $priorities[0]);
    }
}
