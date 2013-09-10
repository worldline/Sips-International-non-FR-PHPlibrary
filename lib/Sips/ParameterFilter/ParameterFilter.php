<?php

namespace Sips\ParameterFilter;

interface ParameterFilter
{
    /** @return array Filtered parameters */
    function filter(array $parameters);
}