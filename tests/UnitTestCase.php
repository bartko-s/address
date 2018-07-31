<?php

declare(strict_types=1);

namespace App\Tests;

use App\Utils;
use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    public function makeNGramDataProvider()
    {
        return [
            ['a', 1, 'a'],
            ['ab', 1, 'a b ab'],
            ['abc', 1, 'a b c ab bc abc'],
            ['a b', 1, 'a b'],
            ['ab cd', 1, 'a b c d ab cd'],
            ['x', 3, 'x'],
            ['abcde', 3, 'abc bcd cde abcd bcde abcde'],
            ['abcde fgh', 3, 'abc bcd cde abcd bcde abcde fgh'],
        ];
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
        return [
            ['a', 'a'],
            ['  b   ', 'b'],
            ['č', 'c'],
            ['Ď', 'd'],
            ['', ''],
            ['ef gh', 'ef & gh'],
            [' xy  z  ', 'xy & z'],
        ];
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
