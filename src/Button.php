<?php

namespace Jeffreyvr\WPMail;

class Button extends MailElement
{
    public function __construct(public Mail $mail, public string $text, public string $url) {}

    public function render(): string
    {
        return $this->mail->viewRender('button', [
            'text' => $this->text,
            'url' => $this->url,
        ]);
    }

    public function plain(): string
    {
        return "$this->text ($this->url)";
    }
}
