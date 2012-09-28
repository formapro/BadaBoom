<?php
namespace BadaBoom\Adapter\Logger;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/28/12
 */
class LogglyLoggerAdapter implements LoggerAdapterInterface
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var int
     */
    private $port;
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $transport;

    public function __construct($key, $port = 443, $host = 'logs.loggly.com')
    {
        $this->key = $key;
        $this->port = $port;
        $this->host = $host;
        
        switch ($port) {
            case '443':
                $this->transport = 'ssl://'.$this->host;
                break;
            case '80':
                $this->transport = $this->host;
                break;
            default:
                throw new \LogicException(sprintf('Invalid port set %s. Supported ports are %s', $this->port, '80, 443'));
        }
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function log($content, $level)
    {
        $fp = fsockopen($this->transport, $this->port, $errno, $errstr, 30);
        if (false == $fp) {
            throw new \LogicException(sprintf('Could not connect to loggly server %s:%s', $this->transport, $this->port));
        }

        $request = "POST /inputs/".$this->key." HTTP/1.1\r\n";
        $request.= "Host: ".$this->host."\r\n";
        $request.= "User-Agent: Badaboom\r\n";
        $request.= "Content-Type: application/json\r\n";
        $request.= "Content-Length: ".strlen($content)."\r\n";
        $request.= "Connection: Close\r\n\r\n";
        $request.= $content;

        fwrite($fp, $request);
        fclose($fp);
    }
}
