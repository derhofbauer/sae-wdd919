<?php

namespace Core;

/**
 * Class Mail
 *
 * @package Core
 * @todo    : comment
 */
class Mail
{

    public array $to = [];
    public array $cc = [];
    public array $bcc = [];
    public string $subject;
    public string $message;
    public string $replyTo;
    public string $from;
    public string $mailer;
    public array $headers = [];
    public array $error;

    /**
     * Mail constructor.
     *
     * @param string|null $to
     * @param string|null $subject
     * @param string|null $message
     *
     * @todo: comment
     */
    public function __construct (string $to = null, string $subject = null, string $message = null)
    {
        $this->mailer = 'PHP/' . phpversion();

        if (!empty($to)) {
            $this->to[] = $to;
        }

        if (!empty($subject)) {
            $this->subject = $subject;
        }

        if (!empty($message)) {
            $this->message = $message;
        }
    }

    public function addTo (string $name, string $email)
    {
        $this->to[] = "$name <$email>";
    }

    public function addCc (string $name, string $email)
    {
        $this->cc[] = "$name <$email>";
    }

    public function addBcc (string $name, string $email)
    {
        $this->bcc[] = "$name <$email>";
    }

    public function addHeader (string $name, string $value)
    {
        $this->headers[$name] = $value;
    }

    public function setFrom (string $name, string $from)
    {
        $this->addHeader('From', "$name <$from>");
    }

    public function send (): bool
    {
        $recipients = $this->prepareRecipients();
        $this->prepareHeaders();

        $success = mail($recipients, $this->subject, $this->message, $this->headers);

        if (!$success) {
            $this->error = error_get_last();
        }

        return $success;
    }

    /**
     * @return string
     */
    private function prepareRecipients (): string
    {
        if (!empty($this->cc)) {
            $this->addHeader('Cc', implode(', ', $this->cc));
        }
        if (!empty($this->bcc)) {
            $this->addHeader('Bcc', implode(', ', $this->bcc));
        }

        return implode(', ', $this->to);
    }

    private function prepareHeaders ()
    {
        $this->addHeader('X-Mailer', $this->mailer);

        if (!empty($this->replyTo)) {
            $this->addHeader('Reply-To', $this->replyTo);
        }
    }

}
