<?php

namespace BadaBoom\Adapter\Mailer;

class NativeMailerAdapter implements MailerAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function send($sender, array $recipients, $subject, $content, array $headers = array())
    {
        $headers['from'] = $sender;
        $headers = $this->prepareHeaders($headers);
        foreach($recipients as $recipient)
        {
            mail($recipient, $subject, $content, $headers);
        }
    }

    /**
     * @param array $headers
     * 
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