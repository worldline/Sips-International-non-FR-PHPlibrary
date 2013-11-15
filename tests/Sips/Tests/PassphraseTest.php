<?php

namespace Sips\Tests;

use Sips\Passphrase;

class PassphraseTest extends \TestCase
{
    /** @test */
    public function PassphraseCanBeCreated()
    {
        $passphrase = new Passphrase("passphrase");
        $this->assertEquals("passphrase", $passphrase);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function PassphraseHasToBeAString()
    {
        $passphrase = new Passphrase(array());
    }
}