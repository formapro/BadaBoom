<?php

namespace BadaBoom\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\UnsupportedException;

use BadaBoom\DataHolder\DataHolderInterface;

class DataHolderNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null)
    {
        $result = array();
        foreach ($object as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    public function denormalize($data, $class, $format = null)
    {


        throw new UnsupportedException('Denormalization is not supported by this normalizer `'.__CLASS__.'`');
    }

    public function supportsNormalization($data, $format = null)
    {
        return  $data instanceof DataHolderInterface;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return false;
    }
}
 
