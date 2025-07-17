<?php

namespace Jeffreyvr\WPMail;

abstract class MailElement
{
    public function render(): string
    {
        return '';
    }

    public function plain(): string
    {
        return '';
    }
}
