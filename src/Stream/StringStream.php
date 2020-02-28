<?php
declare(strict_types=1);

namespace Genkgo\Mail\Stream;

use Genkgo\Mail\StreamInterface;

final class StringStream implements StreamInterface
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->text;
    }
    
    public function close(): void
    {
        return;
    }

    /**
     * @return mixed
     */
    public function detach()
    {
        $handle = \fopen('php://memory', 'r+');
        if ($handle === false) {
            throw new \UnexpectedValueException('Cannot open php://memory for writing');
        }

        \fwrite($handle, $this->text);
        return $handle;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return \strlen($this->text);
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    public function tell(): int
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function eof(): bool
    {
        return $this->position >= \strlen($this->text);
    }

    /**
     * @return bool
     */
    public function isSeekable(): bool
    {
        return true;
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function seek(int $offset, int $whence = SEEK_SET): int
    {
        $length = \strlen($this->text);
        if ($length < $offset) {
            $offset = $length;
        }

        $this->position = $offset;
        return 0;
    }

    /**
     * @return bool
     */
    public function rewind(): bool
    {
        $this->position = 0;
        return true;
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        return true;
    }

    /**
     * @param string $string
     * @return int
     */
    public function write($string): int
    {
        $this->text = \substr_replace($this->text, $string, $this->position, \strlen($string));
        $bytesWritten = \strlen($string);
        $this->position += $bytesWritten;
        return $bytesWritten;
    }

    /**
     * @return bool
     */
    public function isReadable(): bool
    {
        return true;
    }

    /**
     * @param int $length
     * @return string
     */
    public function read(int $length): string
    {
        $result = \substr($this->text, $this->position, $length);
        $this->position += \strlen($result);
        return $result;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return \substr($this->text, $this->position);
    }

    /**
     * @param array<string, mixed> $keys
     * @return array<string, mixed>
     */
    public function getMetadata(array $keys = []): array
    {
        return [];
    }
}
