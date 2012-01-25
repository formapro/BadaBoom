<?php

namespace BadaBoom\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\UnsupportedException;

use BadaBoom\DataHolder\DataHolderInterface;

class DataHolderNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($data, $format = null)
    {
        if (false == $this->supportsNormalization($data, $format)) {
            throw new UnsupportedException(sprintf(
                'Normalization of %s to format %s is not supported by this normalizer.',
                gettype($data),
                $format
            ));
        }

        $result = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->normalizeArray($value);
            } else if (is_object($value)) {
                $result[$key] = var_export($value, true);
            } else if (is_scalar($value)){
                $result[$key] = $this->normalizeScalar($value);
            } else {
                $result[$key] = var_export($value);
            }
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function normalizeArray(array $value)
    {
        array_walk($value, function(&$item) {
            if (is_object($item) || is_array($item)) {
                $item = var_export($item, true);
            }
        });

        return $value;
    }

    /**
     * @param object $value
     *
     * @return string
     */
    protected function normalizeObject($value)
    {
        return var_export($value, true);
    }

    /**
     * @param scalar $value
     *
     * @return string
     */
    protected function normalizeScalar($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null)
    {
        throw new UnsupportedException('Denormalization of any formats is not supported.');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return  $data instanceof DataHolderInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return false;
    }
}