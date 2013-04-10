<?php
namespace BadaBoom\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\NormalizationAwareInterface;
use Symfony\Component\Serializer\Exception\UnsupportedException;

use BadaBoom\Context;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 10/16/12
 */
class LineEncoder implements EncoderInterface, NormalizationAwareInterface
{
    /**
     * @const string
     */
    const DEFAULT_LINE_FORMAT = "[%datetime%] %class%: %message% in file %file% on line %line%\n";

    /**
     * ISO 8601
     * 
     * @const string
     */
    const DEFAULT_DATETIME_FORMAT = 'c';

    /**
     * @var string
     */
    protected $lineFormat;

    /**
     * @var string
     */
    protected $dateFormat;

    /**
     * @param string|null $lineFormat
     * @param string|null $dateFormat
     */
    public function __construct($lineFormat = null, $dateFormat = null)
    {
        $this->lineFormat = $lineFormat ?: static::DEFAULT_LINE_FORMAT;
        $this->dateFormat = $dateFormat ?: static::DEFAULT_DATETIME_FORMAT;
    }
    
    /**
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = array())
    {
        if (false == $data instanceof Context) {
            throw new UnsupportedException(sprintf(
                '%s is not supported by this encoder. Expected instance of BadaBoom\Context',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }
        
        $exception = $data->getException();
        
        $parameters = array(
            'datetime' => $this->getCurrentDate()->format($this->dateFormat), 
            'class' => $this->getShortClassName($exception), 
            'message' => $exception->getMessage() ?: 'unknown',
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
        );
        
        $line = $this->lineFormat;
        foreach ($parameters as $name => $value) {
            $line = str_replace('%'.$name.'%', $value, $line);
        }
        
        return $line;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        return 'line' === $format; 
    }

    /**
     * @return \DateTime
     */
    protected function getCurrentDate()
    {
        return new \DateTime();
    }

    /**
     * @param \Exception $exception
     * 
     * @return string
     */
    protected function getShortClassName(\Exception $exception)
    {
        $ro = new \ReflectionObject($exception);
        
        return $ro->getShortName();
    }
}
