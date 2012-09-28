<?php

namespace BadaBoom\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\EncoderInterface;

class JsonEncoder implements EncoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function encode($data, $format)
    {
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        return 'json' === $format;
    }
}