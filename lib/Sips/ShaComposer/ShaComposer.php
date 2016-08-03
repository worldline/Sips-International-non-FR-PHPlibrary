<?php

namespace Sips\ShaComposer;

/**
 * SHA Composers interface
 */
interface ShaComposer
{
    /**
     * Compose SHA string based on Sips response parameters
     * @param array $parameters
     */
    public function compose(array $parameters);
}
