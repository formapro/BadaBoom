<?php

namespace BadaBoom\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\EncoderInterface;

class TextEncoder implements EncoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function encode($data, $format)
    {
        $text = '';
        foreach ($data as $key => $value) {

            $text .= ucfirst($key);

            $text .= is_array($value) ? $this->encodeArray($value) . "\n\n" : $this->encodeNotArray($value);
        }

        return $text;
    }

    /**
     * @param array $value
     *
     * @return string
     */
    protected function encodeArray(array $value)
    {
        $text = '';
        foreach ($value as $key => $value) {
            $value = (string) $value;
            $value = trim($value);

            $text .= "\n\t" . ucfirst($key) . ': ' . str_replace("\n", "\n\t\t", $value);
        }

        return $text;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function encodeNotArray($value)
    {
        $value = (string) $value;
        $value = trim($value);
        $value = str_replace("\n", "\n\t", $value);

        return "\n\t{$value}\n";
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        return 'plain-text' === $format;
    }
}