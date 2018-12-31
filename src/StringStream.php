<?php namespace Kshabazz\Slib;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Class StringStream
 *
 * @package Kshabazz\Slib
 */
class StringStream implements StreamInterface
{
    const
        ERROR_CLOSED = 1,
        ERROR_REWWIND = 2,
        ERROR_WRITE = 3,
        ERROR_TELL = 4,
        ERROR_SEEK = 5;

    /** @var resource Temp file resource. */
    private $output;

    /**
     * StringStream constructor.
     *
     * @param string $string
     */
    public function __construct($string)
    {
        $this->output = \fopen('php://temp', 'r+');

        \fputs($this->output, $string);

        \rewind($this->output);
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        $contents = $this->getContents();

        return $contents;
    }

    /**
     * Close the stream.
     */
    public function close()
    {
        \fclose($this->output);
    }

    /**
     * @return resource|null
     */
    public function detach()
    {
        $resource = $this->output;

        $this->output = null;

        return $resource;
    }

    /**
     * @return null
     */
    public function getSize()
    {
        return null;
    }

    /**
     * Get the current position of the resource read pointer.
     *
     * @return mixed
     */
    public function tell()
    {
        $position = \ftell($this->output);

        return $position;
    }

    /**
     * @return mixed
     */
    public function eof()
    {
        return \feof($this->output);
    }

    /**
     * @return mixed
     */
    public function isSeekable()
    {
        return false;
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return mixed
     */
    public function seek($offset, $whence = \SEEK_SET)
    {
        throw new RuntimeException('Cannot perform seek on this stream.', self::ERROR_SEEK);
    }

    /**
     * @return mixed
     */
    public function rewind()
    {
        throw new RuntimeException('Cannot rewind the stream.', self::ERROR_REWWIND);
    }

    /**
     * @return mixed
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * @param string $string
     * @return mixed
     */
    public function write($string)
    {
        throw new RuntimeException('Cannot rewind the stream.', self::ERROR_WRITE);
    }

    /**
     * @return mixed
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * @param int $length
     * @return mixed
     */
    public function read($length)
    {
        return \stream_get_contents($this->output, $length);
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        if (!\is_resource($this->output)) {
            throw new RuntimeException('The resource has been closed, unable to get contents.', self::ERROR_CLOSED);
        }

        return \stream_get_contents($this->output);
    }

    /**
     * @param null|string|null $key
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        $metadata = \stream_get_meta_data($this->output);
        $metadata['seekable'] = false;
        $metadata['mode'] = 'rb';

        return $metadata;
    }

}