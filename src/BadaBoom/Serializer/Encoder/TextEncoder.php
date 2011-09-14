<?php

namespace BadaBoom\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\EncoderInterface;

class TextEncoder implements EncoderInterface
{
    public function encode($data, $format)
    {
        $text = '';
        foreach ($data as $key => $value) {

            $text .= ucfirst($key);

            $text .= $this->encodeValue($value) . "\n\n";
        }

        return $text;
    }

    protected function encodeValue(array $value)
    {
        $text = '';
        foreach ($value as $key => $value) {
            $text .= "\n\t" . ucfirst($key) . ': ' . str_replace("\n", "\n\t\t", $value);
        }

        return $text;
    }
}