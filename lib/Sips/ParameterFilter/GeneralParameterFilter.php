<?php

namespace Sips\ParameterFilter;

class GeneralParameterFilter implements ParameterFilter
{
    public function filter(array $parameters)
    {
        array_walk($parameters, 'trim');
        
        return $parameters;
    }
    
}