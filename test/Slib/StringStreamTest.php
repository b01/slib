<?php namespace Kshabazz\Slib\Tests;

use Kshabazz\Slib\StringStream;
use PHPUnit\Framework\TestCase;

/**
 * Class StringStreamTest
 * @package Kshabazz\Slib\Tests
 * @coversDefaultClass \Kshabazz\Slib\StringStream
 */
class StringStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getContents
     */
    public function testCanSetAndGetAContentsOfStream()
    {
        $ss = new StringStream('test');

        $this->assertEquals('test', $ss->getContents());
    }

    /**
     * @covers ::__toString
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::getContents
     */
    public function testCanGetContentsWhenCastingToAString()
    {
        $ss = new StringStream('test');

        $this->assertEquals('test', (string) $ss);
    }

    /**
     * @covers ::getMetadata
     * @uses \Kshabazz\Slib\StringStream::__construct
     */
    public function testCanGetMetaData()
    {
        $ss = new StringStream('test');
        $actual = $ss->getMetadata();

        $this->assertTrue(\is_array($actual));
    }

    /**
     * @covers ::close
     * @covers ::getContents
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @expectedException \RuntimeException
     */
    public function testCanCloseStream()
    {
        $ss = new StringStream('test');

        $ss->close();

        $this->assertEquals('test', $ss->getContents());
    }

    /**
     * @covers ::read
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     */
    public function testCanReadAFewCharsAtATime()
    {
        $ss = new StringStream('test');

        $actual = $ss->read(1);

        $this->assertEquals('t', $actual);

        $ss->close();
    }

    /**
     * @covers ::isWritable
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     */
    public function testIsNotWritable()
    {
        $ss = new StringStream('test');

        $this->assertFalse($ss->isWritable());

        $ss->close();
    }

    /**
     * @covers ::isReadable
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     */
    public function testIsReadable()
    {
        $ss = new StringStream('test');

        $this->assertTrue($ss->isReadable());

        $ss->close();
    }

    /**
     * @covers ::write
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     * @expectedException \RuntimeException
     */
    public function testCannotWriteToTheStream()
    {
        $ss = new StringStream('test');

        $ss->write('1');

        $ss->close();
    }

    /**
     * @covers ::rewind
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     * @expectedException \RuntimeException
     */
    public function testCannotRewind()
    {
        $ss = new StringStream('test');

        $ss->rewind();

        $ss->close();
    }

    /**
     * @covers ::isSeekable
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     */
    public function testIsSeekable()
    {
        $ss = new StringStream('test');

        $this->assertFalse($ss->isSeekable());

        $ss->close();
    }

    /**
     * @covers ::detach
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @expectedException \RuntimeException
     */
    public function testCanDetach()
    {
        $ss = new StringStream('test');

        $actual = $ss->detach();

        $ss->getContents();

        $this->assertTrue(\is_resource($actual));

        \fclose($actual);
    }

    /**
     * @covers ::tell
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     */
    public function testCanTellTheCurrentPosition()
    {
        $ss = new StringStream('test');

        $ss->read(2);

        $actual = $ss->tell();

        $this->assertEquals(2, $actual);

        $ss->close();
    }

    /**
     * @covers ::eof
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     */
    public function testCanCheckForEof()
    {
        $ss = new StringStream('test');

        $ss->getContents();

        $actual = $ss->eof();

        $this->assertTrue($actual);

        $ss->close();
    }

    /**
     * @covers ::getSize
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     */
    public function testCannotGetSize()
    {
        $ss = new StringStream('test');

        $actual = $ss->getSize();

        $this->assertNull($actual);

        $ss->close();
    }

    /**
     * @covers ::seek
     * @uses \Kshabazz\Slib\StringStream::__construct
     * @uses \Kshabazz\Slib\StringStream::close
     * @expectedException \RuntimeException
     */
    public function testCannotPerformSeek()
    {
        $ss = new StringStream('test');

        $ss->seek(1);

        $ss->close();
    }
}
