<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Tests\UnitTestCase;
use App\Utils;

class UtilsTest extends UnitTestCase
{
    public function makeNGramDataProvider()
    {
        return array(
            array('a', 1, 'a'),
            array('ab', 1, 'a b ab'),
            array('abc', 1, 'a b c ab bc abc'),
            array('a b', 1, 'a b'),
            array('ab cd', 1, 'a b c d ab cd'),
            array('x', 3, 'x'),
            array('abcde', 3, 'abc bcd cde abcd bcde abcde'),
            array('abcde fgh', 3, 'abc bcd cde abcd bcde abcde fgh'),
        );
    }

    /**
     * @dataProvider makeNGramDataProvider
     */
    public function testMakeNGram($input, $length, $output)
    {
        $result = Utils::makeNGrams($input, $length);

        $outputAsArray = explode(' ', $output);
        sort($outputAsArray);

        $resultAsArray = explode(' ', $result);
        sort($resultAsArray);

        $this->assertEquals($outputAsArray, $resultAsArray);
    }

    public function createQuerySearchStringProvider()
    {
        return array(
            array('a', 'a'),
            array('  b   ', 'b'),
            array('č', 'c'),
            array('Ď', 'd'),
            array('', ''),
            array('ef gh', 'ef & gh'),
            array(' xy  z  ', 'xy & z'),
        );
    }

    /**
     * @dataProvider createQuerySearchStringProvider
     */
    public function testCreateQuerySearchString($input, $output)
    {
        $result = Utils::createQuerySearchString($input);

        $outputAsArray = explode(' ', $output);
        sort($outputAsArray);

        $resultAsArray = explode(' ', $result);
        sort($resultAsArray);

        $this->assertEquals($outputAsArray, $resultAsArray);
    }
}
