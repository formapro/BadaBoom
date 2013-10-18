<?php
/**
 * @var $this \BadaBoom\Serializer\Encoder\HtmlEncoder
 * @var $exception \Symfony\Component\Debug\Exception\FlattenException
 * @var $pageURL string
 * @var $vars array
 *
 * @return string
 */
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="robots" content="noindex,nofollow"/>
    <title><?php echo "({$exception->getStatusCode()}) {$exception->getMessage()}" ?></title>
    <?php $h_style = "font-family: Georgia, 'Times New Roman', Times, serif; color: #313131; margin: 10px 0; padding: 0;"  ?>
    <?php $td_style = "text-align: left; padding: 20px; background-color: #fff; margin-bottom: 50px; border-spacing: 50px; border: 1px solid #dfdfdf; border-radius: 16px;" ?>
</head>
<body class="" style="width: 700px; background: #eee; padding: 0 20px; margin: 0 auto;">

<table style="width: 100%; overflow: hidden; border-collapse: separate; border-spacing: 0 20px;"><tbody>
    <tr>
        <td class="exception-head" style="<?php echo $td_style ?> text-align: center; background-color: #f6f6f6">
            <h1 style="<?php echo $h_style ?> font-size: 20px;"><?php echo $exception->getMessage() ?></h1>
            <strong><?php echo $exception->getStatusCode() ?></strong>
            <?php echo \Symfony\Component\HttpFoundation\Response::$statusTexts[$exception->getStatusCode()] ?> - <?php $this->abbrClass($exception->getClass()) ?>
        </td>
    </tr>
    <tr>
        <td style="<?php echo $td_style ?>">
            <h3 style="<?php echo $h_style ?>">Information:</h3>
            <?php $def_td_dtyle = 'vertical-align: top; width: 100px;' ?>
            <table>
                <tr>
                    <td style="<?php echo $def_td_dtyle ?>"><strong>Generated at: </strong></td>
                    <td><?php echo date("d-m-Y H:i:s") ?></td>
                </tr>
                <tr>
                    <td style="<?php echo $def_td_dtyle ?>"><strong>Class name: </strong></td>
                    <td><?php echo $exception->getClass() ?></td>
                </tr>
                <tr>
                    <td style="<?php echo $def_td_dtyle ?>"><strong>Message: </strong></td>
                    <td><?php echo $exception->getMessage() ?></td>
                </tr>
                <tr>
                    <td style="<?php echo $def_td_dtyle ?>"><strong>Uri: </strong></td>
                    <td><a href="<?php echo $pageURL ?>"><?php echo $pageURL ?></a></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="<?php echo $td_style ?>">
            <h3 style="<?php echo $h_style ?>">Stack trace:</h3>

            <ol class="traces list_exception" id="traces" style="padding: 0 0 0 10px;">
            <?php foreach ($exception->getTrace() as $i => $trace) : ?>
                <li style="margin-bottom: 20px">
                    <?php if (!empty($trace['class'])) : ?>
                        at
                        <strong>
                            <?php echo "{$this->abbrClass($trace['class'], 1)} {$trace['type']} {$trace['function']}" ?>
                        </strong>
                        (<?php echo $this->formatArgs((array) $trace['args']) ?>)
                    <?php else : ?>
                        <?php echo $trace['function'] ?>
                    <?php endif; ?>

                    <?php if (!empty($trace['file']) && !empty($trace['line'])) : ?>
                        <?php echo !empty($trace['function']) ? '<br />' : '' ?>
                        in <?php echo $this->formatFile($trace['file']) ?> at line <?php echo $trace['line'] ?> &nbsp;
                        <br/><br/>
                        <div>
                            <?php echo $this->fileExcerpt($trace['file'], $trace['line']) ?>
                        </div>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
            </ol>
        </td>
    </tr>

    <?php foreach ($vars as $title => $list) : ?>
        <tr>
            <td style="<?php echo $td_style ?>">
                <h3 style="<?php echo $h_style ?>">
                    <?php echo $title ?>:
                </h3>
                <?php if (!empty($list)) : ?>
                    <table style="width: 100%; border: 1px solid #ddd">
                        <tr>
                            <th style="text-align: left; padding: 5px 20px; background-color: #ccc;">Name</th>
                            <th style="text-align: left; padding: 5px 20px; background-color: #ccc;">Value</th>
                        </tr>
                        <?php $i = 0; foreach ($list as $key => $value) : ?>
                        <tr style="<?php if ($i%2 == 0) : ?>background: #eee;<?php endif  ?>">
                            <th style="padding: 5px 20px; width: 1px"><?php echo $key ?></th>
                            <td style="padding: 5px 20px; max-width: 600px; overflow: hidden"><?php echo $value ?></td>
                        </tr>
                        <?php $i++; endforeach ?>
                    </table>
                    <small>* hover 'object' and 'array' types to see variables</small>
                <?php else : ?>
                    <p><em>No variables found</em></p>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach ?>
</tbody></table>
</body>
</html>