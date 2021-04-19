# Form finisher for NeosRulez.DirectMail

Form finisher to add recipients with Neos.Form.

## Installation

The NeosRulez.DirectMail.FormFinisher package is listed on packagist (https://packagist.org/packages/neosrulez/directmail-formfinisher) - therefore you don't have to include the package in your "repositories" entry any more.

Just run:

```
composer require neosrulez/directmail-formfinisher
```

## Usage

Create the finisher, create and configure a form field of the type ``NeosRulez.DirectMail.FormFinisher:RecipientList`` and a checkbox with the identifier ``acceptDirectMail``. The finisher expects a form field with the name ``email``.

## Author

* E-Mail: mail@patriceckhart.com
* URL: http://www.patriceckhart.com 
