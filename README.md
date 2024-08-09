# Kasikornbank(Kbank) Magento 2 Payments Gateway Module #

    composer require ghoster/module-kbankpayments

[![Latest Stable Version](https://poser.pugx.org/ghoster/module-kbankpayments/v)](https://packagist.org/packages/ghoster/module-kbankpayments)
[![Latest Unstable Version](https://poser.pugx.org/ghoster/module-kbankpayments/v/unstable)](https://packagist.org/packages/ghoster/module-kbankpayments)
[![License](https://poser.pugx.org/ghoster/module-outofstockatlast/license)](https://packagist.org/packages/ghoster/module-outofstockatlast)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/thinghost)
[![Build Status](https://github.com/tuyennn/magento2-kbank-payment/actions/workflows/coding-standard.yml/badge.svg)](https://github.com/tuyennn/magento2-kbank-payment/actions/workflows/coding-standard.yml)
---

## Features

* Module to add payment method Kbank (Kasikornbank) for Embedded UI Method
* Support 3DS
* Support Installment

![kbank_embedded_installment](./.github/Screenshot/kbank_embedded_installment.png)
![kbank_embedded_full_payment](./.github/Screenshot/kbank_embedded_full_payment.png)

## Compatibility 

| Magento Version (Open Source/Commerce) | Supported |
| -------------------------------------- | --------- |
| **2.0.x**                              | No ❌      |
| **2.1.x**                              | No ❌      |
| **2.2.x**                              | No ❌      |
| **<2.3.2**                             | No ❌      |
| **<2.3.5**                             | No ❌      |
| **>=2.3.5**                            | No ❌      |
| **2.4.0**                              | Yes ✔️     |
| **>=2.4.1 && < 2.4.6**                 | Yes ✔️     |
| **>=2.4.6**                            | Yes ✔️     |

## Configurations
* Require the public and secret key from the Gateway:
* `<base_url>/kbank/payment/callback` for Card Payment Callback URL: (Embedded Method only)
* `<base_url>/rest/V1/kbank/payment/notify` for Card Payment Notify URL: (Embedded Method only)

![Configuration](./.github/Screenshot/kbank_payment_configuration.png)

## Donation

If this project help you reduce time to develop, you can give me a cup of beer :)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/thinghost)
