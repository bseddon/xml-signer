# xml-signer
Provides signing and verification of XML documents for PHP and includes support for XAdES.

This project builds on [xmlseclibs]() by Rob Richards to add XAdES-BES and XAdES-T form support.
The underlying xmlseclibs has been modified to support detached signatures.  To ensure code in
this project does not conflict with code from the original project the namespaces have been
changed to\lyquidity\xmlsig

## How to Install

Install with [`composer.phar`](http://getcomposer.org).

```sh
php composer.phar require "bsseddon/xml-signer"
```
