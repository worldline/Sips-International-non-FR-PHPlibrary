<?php

namespace Sips\Tests\ShaComposer;

use Sips\ShaComposer\ShaComposer;

/**
 * Fake SHA Composer to decouple tests from actual SHA composers
 */
class FakeShaComposer implements ShaComposer
{
    const FAKESHASTRING = 'foo';
    
    public function compose(array $responseParameters)
    {
        return self::FAKESHASTRING;
    }
}