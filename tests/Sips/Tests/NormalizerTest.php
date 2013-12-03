<?php

namespace Sips\Tests;

use Sips\Normalizer;

class NormalizerTest extends \TestCase {

    /**
     * @test
     */
    public function CanNormalizeStrings()
    {
        $string = "Màthìéû Dùffèlêr";
        $this->assertEquals("Mathieu Duffeler", Normalizer::normalize($string));
    }
} 