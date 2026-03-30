<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>
{!! $head ?? '' !!}
</head>
<body>

<table @class(['wrapper']) width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table @class(['content']) width="100%" cellpadding="0" cellspacing="0" role="presentation">
{!! $header ?? '' !!}

<!-- Email Body -->
<tr>
<td @class(['body']) width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table @class(['inner-body']) align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td @class(['content-cell'])>
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}

<table class="panel" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top: 30px; border-top: 1px solid #E8E5EF; padding-top: 20px;">
<tr>
<td class="panel-content">
<p style="font-size: 14px; color: #718096; margin-bottom: 0;">
    <strong>Cần hỗ trợ?</strong><br>
    Liên hệ với chúng tôi qua email: <a href="mailto:support@ndhshop.com" style="color: #3869D4;">support@ndhshop.com</a>
    hoặc <br> Hotline: <a href="tel:+84388937608" style="color: #3869D4; cursor: pointer; text-decoration: underline;">+84 388937608</a>
</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>
