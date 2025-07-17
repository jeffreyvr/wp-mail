<p align="center"><a href="https://vanrossum.dev" target="_blank"><img src="https://raw.githubusercontent.com/jeffreyvr/vanrossum.dev-art/main/logo.svg" width="320" alt="vanrossum.dev Logo"></a></p>

<p align="center">
<a href="https://packagist.org/packages/jeffreyvanrossum/wp-mail"><img src="https://img.shields.io/packagist/dt/jeffreyvanrossum/wp-mail" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/jeffreyvanrossum/wp-mail"><img src="https://img.shields.io/packagist/v/jeffreyvanrossum/wp-mail" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/jeffreyvanrossum/wp-mail"><img src="https://img.shields.io/packagist/l/jeffreyvanrossum/wp-mail" alt="License"></a>
</p>

# WP Mail

For those wanting to send good looking WordPress emails with a fluent API.

## Installation

```bash
composer require jeffreyvanrossum/wp-mail
```

## Usage

You can either build your email using the builder pattern or you can use a view.

```php
use Jeffreyvr\WPMail\Mail;

Mail::make()
    ->to('jane@doe.com')
    ->subject('Hello')
    ->line(text: 'Hello there')
    ->button(text: 'Click me', url: 'https://vanrossum.dev')
    ->line(text: 'Kind regards, Jeffrey')
    ->send();
```

### Available Methods

- `subject(string $subject)` - Set the email subject
- `to(string $email, string $name = '')` - Add recipient
- `cc(string $email, string $name = '')` - Add CC recipient
- `bcc(string $email, string $name = '')` - Add BCC recipient
- `from(string $email, string $name = '')` - Set sender information
- `line(string $text)` - Add a text line to the email
- `button(string $text, string $url)` - Add a call-to-action button
- `view(string $path, array $data = [])` - Use a view template
- `send()` - Send the email

In the view you can use `$mail` to access the mail object.

## Want to schedule emails as a background job?

Take a look at [wp-job-scheduler](https://github.com/jeffreyvr/wp-job-scheduler). Simply create a job and add the mail sending logic to the handle method.

## Credits
- [Responsive HTML Email Template](https://github.com/leemunroe/responsive-html-email-template)

## Contributors
* [Jeffrey van Rossum](https://github.com/jeffreyvr)
* [All contributors](https://github.com/jeffreyvr/wp-mail/graphs/contributors)

## License
MIT. Please see the [License File](/LICENSE) for more information.
