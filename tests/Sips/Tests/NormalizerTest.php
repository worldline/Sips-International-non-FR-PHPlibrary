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

    /**
     * @test
     */
    public function CanNormalizeHTMLTags()
    {
        $string = "Màthìéû Dùffèlêr<a href=\"test\">test</a>";
        $this->assertEquals("Mathieu Duffeler a href=\"test\" test /a ", Normalizer::normalize($string));
    }
}