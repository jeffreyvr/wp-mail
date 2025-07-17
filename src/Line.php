<?php

namespace Jeffreyvr\WPMail;

class Line extends MailElement
{
    public function __construct(public Mail $mail, public string $text) {}

    public function render(): string
    {
        return $this->mail->viewRender('line', [
            'text' => $this->text,
        ]);
    }

    public function plain(): string
    {
        return $this->text;
    }
}
