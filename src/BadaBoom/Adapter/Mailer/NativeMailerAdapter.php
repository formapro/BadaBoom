<?php

namespace BadaBoom\Adapter\Mailer;

class NativeMailerAdapter implements MailerAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function send($from, array $to, $subject, $content, array $additionalHeaders = array())
    {
        $additionalHeaders['from'] = $from;
        $headers = $this->prepareHeaders($additionalHeaders);
        foreach($to as $recipient)
        {
            mail($recipient, $subject, $content, $headers);
        }
    }

    /**
     * @param array $headers
     * @return string
     */
    protected function prepareHeaders(array $headers = array())
    {
        $preparedHeaders = '';

        foreach($headers as $field => $content)
        {
            $fieldComponents = explode('-', $field);
            array_walk($fieldComponents, function(&$fieldComponent) {
                $fieldComponent = ucfirst(strtolower($fieldComponent));
            });
            $field = implode('-', $fieldComponents);

            $preparedHeaders .= sprintf("%s: %s \r\n", $field, $content);
        }

        return $preparedHeaders;
    }
}