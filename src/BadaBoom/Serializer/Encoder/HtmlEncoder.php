<?php

namespace BadaBoom\Serializer\Encoder;

use BadaBoom\Context;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerBag;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\NormalizationAwareInterface;

class HtmlEncoder implements EncoderInterface, NormalizationAwareInterface
{
    private $abbrStyle = 'border: 0; border-bottom: 1px dotted #000; cursor: help; font-variant: normal;';

    public function __construct() {}

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        return 'html' === $format;
    }

    /**
     * @param Context $data
     * @param string $format
     * @param array $context
     *
     * @return string
     */
    public function encode($data, $format, array $context = array())
    {
        // Email template location
        $tpl = __DIR__ . '/views/exceptionEmail.html.php';
        if (file_exists($tpl)) {
            // Get exception
            $exception = FlattenException::create($data->getException());

            // Get trace array
            $trace = $data->getException()->getTrace();

            // Build URL of the current page using the $_SERVER variables
            $pageURL = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 'https' : 'http';
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }

            // Wrap the Server array to sf2 bag (need to get all the request headers in easy way)
            $server = new ServerBag($_SERVER);

            // First, create a list of several data-arrays from the native php arrays
            $vars = [
                'Scope variables'            => $this->normalizeData($context),
                'Request GET parameters'     => $this->normalizeData($_GET),
                'Request POST parameters'    => $this->normalizeData($_POST),
                'Request cookies'            => $this->normalizeData($_COOKIE),
                'Request server parameters'  => $this->normalizeData($server->all()),
                'Request session parameters' => [],
                'Request headers'            => $this->normalizeData($server->getHeaders()),
            ];
            if (isset($_SESSION)) {
                $vars['Request session parameters'] = $this->normalizeData($_SESSION);
            }

            // Then looking for the sf Request object
            // And complementary and replace data-arrays in the list
            $traceArgs = array_pop($trace)['args'];
            if (isset($traceArgs[0]) && $traceArgs[0] instanceof Request) {
                /** @var Request $r */
                $request = $traceArgs[0];
                $vars = array_merge($vars, [
                    'Request GET parameters'     => $this->normalizeData($request->query->all()),
                    'Request POST parameters'    => $this->normalizeData($request->request->all()),
                    'Request attributes'         => $this->normalizeData($request->attributes->all()),
                    'Request cookies'            => $this->normalizeData($request->cookies->all()),
                    'Request headers'            => $this->normalizeData($request->headers->all()),
                    'Request server parameters'  => $this->normalizeData($request->server->all()),
                    'Request session parameters' => $this->normalizeData($request->getSession()->all())
                ]);
            }

            // Generate the template
            ob_start();
            include_once $tpl;
            return ob_get_clean();
        } else {
            // If can not find - use the simplified text encoder
            $textEncoder = new TextEncoder();
            return $textEncoder->encode($data, $format, $context);
        }
    }

    /**
     * Prepare data for pretty print and solve problems with recursive objects and arrays
     *
     * @param mixed $data
     *
     * @return array
     */
    private function normalizeData($data)
    {
        $data = (array) $data;
        $result = [];
        foreach ($data as $key => $value) {
            if (null === $value) {
                $result[$key] = '<font color="#3465a4">null</font>';
            } elseif (is_bool($value)) {
                $bool = true === $value ? 'true' : 'false';
                $result[$key] = '<small>boolean</small> <font color="#75507b">'.$bool.'</font>';
            } elseif (is_string($value)) {
                $result[$key] = '<small>string</small> <font color="#cc0000">\''.$value.'\'</font> <small><i>(length='.strlen($value).')</i></small>';
            } elseif (is_int($value) || is_float($value)) {
                $result[$key] = '<small>int</small> <font color="#4e9a06">'.$value.'</font>';
            } elseif (is_array($value)) {
                $result[$key] = '<abbr style="'.$this->abbrStyle.'" title="'.$this->getTitleForObject($value).'"><b>array</b></abbr> <small><i>(size='.count($value).')</i></small>';
            } elseif (is_object($value)) {
                $class = get_class($value);
                $className = explode('\\', $class);
                $className = array_pop($className);
                $result[$key] = '<abbr style="'.$this->abbrStyle.'" title="'.$this->getTitleForObject($value).'"><b>object</b></abbr>(<abbr title="'.$class.'">'.$className.'</abbr>)';
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Generate a string with key-value pairs for objects and arrays
     *
     * @param $value
     *
     * @return string
     */
    private function getTitleForObject($value)
    {
        $title = '';

        if (is_array($value) || is_object($value)) {
            foreach ($value as $objKey => $objValue) {
                if (is_object($objValue)) {
                    $title .= '[' . $objKey . '] => object('.get_class($objValue).')' . "\n";
                } elseif (is_array($objValue)) {
                    $title .= '[' . $objKey . '] => array(size='.count($objValue).')' . "\n";
                } else {
                    $title .= '[' . $objKey . '] => ' . $objValue . "\n";
                }
            }
            if (empty($title)) {
                $title = 'empty';
            }
        }

        return $title;
    }

    /**
     * Truncate a class name and wrap with <abbr> tag
     * @param string $name
     * @param bool $return
     *
     * @return bool|string
     */
    public function abbrClass($name, $return = false) {
        $parts = explode('\\', $name);
        if ($return) {
            return sprintf('<abbr title="%s" style="%s">%s</abbr>', $name, $this->abbrStyle, array_pop($parts));
        } else {
            printf('<abbr title="%s" style="%s">%s</abbr>', $name, $this->abbrStyle, array_pop($parts));
            return true;
        }
    }

    /**
     * Truncate a file path and wrap with <abbr> tag if need.
     *
     * @param string  $file An absolute file path
     *
     * @return string
     */
    public function formatFile($file)
    {
        $maxLen = 70;
        $blockLen = ($maxLen / 2) - 5;

        if (strlen($file) > $maxLen) {
            $text = '<abbr style="'.$this->abbrStyle.'" title="'.$file.'">' . substr($file, 0, $blockLen) . ' ... ' . substr($file, -$blockLen) . '</abbr>';
        } else {
            $text = $file;
        }

        return $text;
    }

    /**
     * Fork from Symfony\Bridge\Twig\Extension\CodeExtension
     * Formats an array as a string.
     *
     * @param array $args The argument array
     *
     * @return string
     */
    public function formatArgs($args)
    {
        $result = array();
        foreach ($args as $key => $item) {
            if ('object' === $item[0]) {
                $parts = explode('\\', $item[1]);
                $short = array_pop($parts);
                $formattedValue = sprintf("<em>object</em>(<abbr title=\"%s\">%s</abbr>)", $item[1], $short);
            } elseif ('array' === $item[0]) {
                $formattedValue = sprintf("<em>array</em>(%s)", is_array($item[1]) ? $this->formatArgs($item[1]) : $item[1]);
            } elseif ('string'  === $item[0]) {
                $formattedValue = sprintf("'%s'", htmlspecialchars($item[1], ENT_QUOTES, 'utf-8'));
            } elseif ('null' === $item[0]) {
                $formattedValue = '<em>null</em>';
            } elseif ('boolean' === $item[0]) {
                $formattedValue = '<em>'.strtolower(var_export($item[1], true)).'</em>';
            } elseif ('resource' === $item[0]) {
                $formattedValue = '<em>resource</em>';
            } else {
                $formattedValue = str_replace("\n", '', var_export(htmlspecialchars((string) $item[1], ENT_QUOTES, 'utf-8'), true));
            }

            $result[] = is_int($key) ? $formattedValue : sprintf("'%s' => %s", $key, $formattedValue);
        }

        return implode(', ', $result);
    }

    /**
     * Fork from Symfony\Bridge\Twig\Extension\CodeExtension
     * Returns an excerpt of a code file around the given line number.
     *
     * @param string $file A file path
     * @param int    $line The selected line number
     *
     * @return string An HTML string
     */
    public function fileExcerpt($file, $line)
    {
        if (is_readable($file)) {
            // highlight_file could throw warnings
            // see https://bugs.php.net/bug.php?id=25725
            $code = @highlight_file($file, true);
            // remove main code/span tags
            $code = preg_replace('#^<code.*?>\s*<span.*?>(.*)</span>\s*</code>#s', '\\1', $code);
            $content = preg_split('#<br />#', $code);

            $lines = array();
            for ($i = max($line - 3, 1), $max = min($line + 3, count($content)); $i <= $max; $i++) {
                $lines[] = '<li'.($i == $line ? ' class="selected"' : '').'><code>'.$this->fixCodeMarkup($content[$i - 1]).'</code></li>';
            }

            return '<ol start="'.max($line - 3, 1).'">'.implode("\n", $lines).'</ol>';
        }

        return '';
    }

    /**
     * Fork from Symfony\Bridge\Twig\Extension\CodeExtension
     *
     * @param string|int $line
     *
     * @return mixed|string
     */
    protected function fixCodeMarkup($line)
    {
        // </span> ending tag from previous line
        $opening = strpos($line, '<span');
        $closing = strpos($line, '</span>');
        if (false !== $closing && (false === $opening || $closing < $opening)) {
            $line = substr_replace($line, '', $closing, 7);
        }

        // missing </span> tag at the end of line
        $opening = strpos($line, '<span');
        $closing = strpos($line, '</span>');
        if (false !== $opening && (false === $closing || $closing > $opening)) {
            $line .= '</span>';
        }

        return $line;
    }
}
