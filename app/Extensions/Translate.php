<?php

namespace App\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class Translate extends AbstractExtension implements GlobalsInterface
{
    private $container = null;

    public function __construct($container)
    {
        $this->container =& $container;
    }


    /**
     * Return functions available for Twig
     *
     * @return array
     */
    public function getFunctions(): array
    {
        if (!function_exists('bindtextdomain') || !function_exists('textdomain')) {
            return array();
        }

        $function = (function_exists('_') ? 'translate' : 'fakeTranslate');

        return array(
            new TwigFunction('__', array($this, $function))
        );
    }


    /**
     *
     * @return string
     */
    public function translate($text): string
    {
        return _($text);
    }


    /**
     *
     * @param mixed $text
     * @return mixed
     */
    public function fakeTranslate($text): string
    {
        return (string) $text;
    }


    /**
     * Assign global variables for the csrf token
     *
     * @return array
     */
    public function getGlobals(): array
    {
        return $this->container->get('clientVariables');
    }
}
