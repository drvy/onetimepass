<?php

namespace App\Extensions;

use Slim\Csrf\Guard;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension implements GlobalsInterface
{
    protected Guard $csrf;


    public function __construct(Guard $csrf)
    {
        $this->csrf = $csrf;
    }


    /**
     * Return functions available for Twig
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return array(
            new TwigFunction('csrf', array($this, 'csrf'))
        );
    }


    /**
     * Return html csrf token inputs
     *
     * @return string
     */
    public function csrf(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">
            <input type="hidden" name="%s" value="%s">',
            $this->csrf->getTokenNameKey(),
            $this->csrf->getTokenName(),
            $this->csrf->getTokenValueKey(),
            $this->csrf->getTokenValue()
        );
    }


    /**
     * Assign global variables for the csrf token
     *
     * @return array
     */
    public function getGlobals(): array
    {
        return array(
            'csrf'   => array(
                'keys' => array(
                    'name'  => $this->csrf->getTokenNameKey(),
                    'value' => $this->csrf->getTokenValueKey(),
                ),
                'name'  => $this->csrf->getTokenName(),
                'value' => $this->csrf->getTokenValue(),
            )
        );
    }
}
