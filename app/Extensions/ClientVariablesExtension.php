<?php

namespace App\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class ClientVariablesExtension extends AbstractExtension implements GlobalsInterface
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
        return array(
            new TwigFunction('clientVariablesScript', array($this, 'clientVariablesScript'))
        );
    }


    /**
     * Return html csrf token inputs
     *
     * @return string
     */
    public function clientVariablesScript(): string
    {
        $payload = json_encode($this->container->get('clientVariables'));
        $script  = sprintf('var clientSettings = JSON.parse(\'%s\');', $payload);
        $hash    = 'sha384-' . base64_encode(hash('sha384', $script, true));

        return sprintf('<script integrity="%s">
            %s
        </script>', $hash, $script);
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
