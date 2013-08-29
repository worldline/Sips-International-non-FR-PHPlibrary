<?php

namespace Sips\ShaComposer;

use Sips\Passphrase;
use Sips\ShaComposer\ShaComposer;

class AllParametersShaComposer implements ShaComposer
{
    /**    
     * @var string Passphrase
     */
    private $passphrase;
    
    public function __construct(Passphrase $passphrase)
    {
        $this->passphrase = $passphrase;
    }
    
    public function compose(array $parameters)
    {
        // compose SHA string
        $shaString = '';
        foreach($parameters as $key => $value) {
            $shaString .= $key . '=' . $value;            
            $shaString .= (array_search($key, array_keys($parameters)) != (count($parameters)-1)) ? '|' : $this->passphrase;                        
        }
        
        return hash('sha256', $shaString);
    }
}