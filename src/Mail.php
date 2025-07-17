<?php

namespace Jeffreyvr\WPMail;

use Jeffreyvr\WPViews\WPViews;

class Mail
{
    public string $viewPath;

    public string $template;

    public ?string $body = null;

    public ?string $plainText = null;

    public string $subject;

    public string $to;

    public ?string $cc = null;

    public ?string $bcc = null;

    public string $from;

    public string $siteName;

    public string $siteUrl;

    public ?string $unsubscribeUrl = null;

    public ?string $unsubscribeText = null;

    public array $data = [];

    public array $content = [];

    public WPViews $views;

    public function __construct()
    {
        $this->viewPath = __DIR__.'/../resources/views/';
        $this->template = 'template';
        $this->siteName = get_option('blogname');
        $this->siteUrl = get_option('home');

        $this->from = get_option('blogname').' <'.get_option('admin_email').'>';

        $this->views = new WPViews($this->viewPath);
    }

    public static function make(): self
    {
        return new self;
    }

    public function view(string $view, array $data = []): self
    {
        $this->body = $this->render($view, $data);

        return $this;
    }

    public function siteName(string $siteName): self
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function siteUrl(string $siteUrl): self
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    public function plainText(string|callable $plainText): self
    {
        if (is_callable($plainText)) {
            $this->plainText = $plainText($this);
        } else {
            $this->plainText = $plainText;
        }

        return $this;
    }

    public function unsubscribeUrl(string $unsubscribeUrl): self
    {
        $this->unsubscribeUrl = $unsubscribeUrl;

        return $this;
    }

    public function unsubscribeText(string $unsubscribeText): self
    {
        $this->unsubscribeText = $unsubscribeText;

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function to(string $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function from(string $name, string $email): self
    {
        $this->from = "$name <{$email}>";

        return $this;
    }

    public function template(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function body(string|callable $body): self
    {
        if (is_callable($body)) {
            $this->body = $body($this);
        } else {
            $this->body = $body;
        }

        return $this;
    }

    protected function getBody(): string
    {
        if ($this->body) {
            return $this->body;
        }

        $content = '';

        foreach ($this->content as $item) {
            $content .= $item->render();
        }

        if (empty($content)) {
            throw new \Exception('No content provided');
        }

        return $this->views->render($this->template, [
            'slot' => $content,
            'mail' => $this,
        ]);
    }

    protected function getPlainText(): string
    {
        if ($this->plainText) {
            return $this->plainText;
        }

        if (empty($this->content)) {
            $plainText = $this->getBody();

            if (empty($plainText)) {
                return '';
            }

            $plainText = preg_replace('/<br\s*\/?\s*>/', "\n", $plainText);
            $plainText = preg_replace('/<style\s*\/?\s*>/', '', $plainText);

            return strip_tags($plainText);
        }

        $content = '';

        foreach ($this->content as $item) {
            $content .= $item->plain()."\n\n";
        }

        return $content;
    }

    public function render(string $view, array $data = []): string
    {
        $path = pathinfo($view, PATHINFO_DIRNAME);
        $file = pathinfo($view, PATHINFO_FILENAME);

        ray($this);

        return (new WPViews($path))->render($file, array_merge($data, [
            'mail' => $this,
        ]));
    }

    public function viewRender(string $view, array $data = []): string
    {
        return $this->views->render($view, array_merge($data, [
            'mail' => $this,
        ]));
    }

    public function line(string $text): self
    {
        $this->content[] = new Line($this, $text);

        return $this;
    }

    public function button(string $text, string $url): self
    {
        $this->content[] = new Button($this, $text, $url);

        return $this;
    }

    public function send(): bool
    {
        if (empty($this->to)) {
            throw new \Exception('To is required');
        }

        if (empty($this->subject)) {
            throw new \Exception('Subject is required');
        }

        if (empty($this->from)) {
            throw new \Exception('From is required');
        }

        $boundary = uniqid('wp_mail_boundary_');

        $headers = [
            'MIME-Version: 1.0',
            "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
            'From: '.$this->from,
        ];

        if ($this->cc) {
            $headers[] = 'CC: '.$this->cc;
        }

        if ($this->bcc) {
            $headers[] = 'BCC: '.$this->bcc;
        }

        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $this->getPlainText()."\r\n\r\n";
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $this->getBody()."\r\n\r\n";
        $message .= "--{$boundary}--";

        return wp_mail($this->to, $this->subject, $message, $headers);
    }
}
