<?php
/*
 * This file is part of the php-code-coverage package.
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class SampleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \SampleTest::testAssertEquals()
     */
    public function testAssertEquals()
    {
        $this->assertEquals('foo', 'foo');
        #$this->assertEquals('foo', 'foo2');
    }

    /**
     * @covers \SampleTest::testAssertSame()
     */
    public function testAssertSame()
    {
        $this->assertSame('first', 'first');
    }

    /**
     * @covers \SampleTest::testAssertTrue()
     */
    public function testAssertTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers \SampleTest::testAssertFalse()
     */
    public function testAssertFalse()
    {
        $this->assertFalse(false);
    }
}
