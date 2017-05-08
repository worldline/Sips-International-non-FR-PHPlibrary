<?php

namespace Sips;

final class Passphrase
{
    /**
     * @var string
     */
    protected $passphrase;
    
    public function __construct($passphrase)
    {
        if (!is_string($passphrase)) {
            throw new \InvalidArgumentException("String expected");
        }
        $this->passphrase = $passphrase;
    }
    
    /**
     * String representation
     */
    public function __toString()
    {
        return (string) $this->passphrase;
    }
}
