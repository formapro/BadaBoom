<?php
namespace BadaBoom\Serializer\Normalizer;

use BadaBoom\Context;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\UnsupportedException;

class RecursionSafeContextNormalizer implements NormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function normalize($data, $format = null, array $context = array())
    {
        /** @var $data Context */
        if (false == $this->supportsNormalization($data, $format)) {
            throw new UnsupportedException(sprintf(
                'Normalization of %s to format %s is not supported by this normalizer.',
                gettype($data),
                $format
            ));
        }

        $result = array();
        foreach ($data->getVars() as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->normalizeArray($value);
            } else if (is_object($value)) {
                $result[$key] = $this->dump($value);
            } else if (is_scalar($value)){
                $result[$key] = $this->normalizeScalar($value);
            } else {
                $result[$key] = $this->dump($value);
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
                $item = $this->dump($item);
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
        return  $this->dump($value);
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
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return  $data instanceof Context;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return false;
    }

    /**
     * Dump variables. Recursion safe and humanized
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function dump(&$varInput, $var_name = '', $reference = '', $method = '=', $sub = false)
    {
        static $output;
        static $depth;
        if ($sub == false) {
            $output = '';
            $depth = 0;
            $reference = $var_name;
            $var = serialize($varInput);
            $var = unserialize($var);
        } else {
            ++$depth;
            $var = &$varInput;
        }
        // constants
        $nl = "\n";
        $block = 'a_big_recursion_protection_block';
        $c = $depth;
        $indent = '';
        while ($c-- > 0) {
            $indent .= '| ';
        }
        $namePrefix = $var_name?$var_name . ' ' . $method:'';
        // if this has been parsed before
        if (is_array($var) && isset($var[$block])) {
            $real = &$var[$block];
            $name = &$var['name'];
            $type = gettype($real);
            $output .= $indent . $namePrefix . '& ' . ($type == 'array'?'Array':get_class($real)) . ' ' . $name . $nl;
            // havent parsed this before
        } else {
            // insert recursion blocker
            $var = Array($block => $var, 'name' => $reference);
            $theVar = &$var[$block];
            // print it out
            $type = gettype($theVar);
            switch ($type) {
                case 'array':
                    $output .= $indent . $namePrefix . ' Array (' . $nl;
                    $keys = array_keys($theVar);
                    foreach ($keys as $name) {
                        $value = &$theVar[$name];
                        $this->dump($value, $name, $reference . '["' . $name . '"]', '=', true);
                    }
                    $output .= $indent . ')' . $nl;
                    break;
                case 'object':
                    $output .= $indent . $namePrefix . get_class($theVar) . ' {' . $nl;
                    foreach ($theVar as $name => $value) {
                        $this->dump($value, $name, $reference . '=>' . $name, '=>', true);
                    }
                    $output .= $indent . '}' . $nl;
                    break;
                case 'string':
                    $output .= $indent . $namePrefix . ' "' . $theVar . '"' . $nl;
                    break;
                default:
                    $output .= $indent . $namePrefix . ' (' . $type . ') ' . $theVar . $nl;
                    break;
            }
        }
        --$depth;
        return $output;
    }
}