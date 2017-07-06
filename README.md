# Billmate Payment Gateway for Opencart
By Billmate AB - http://billmate.se/

Documentation with instructions on how to setup the plugin can be found at https://billmate.se/plugins/manual/Installation_Manual_Opencart_Billmate.pdf


## DESCRIPTION

Billmate Payment Gateway is a plugin that extends OpenCart, allowing your customers to get their products first and pay by invoice to Billmate later (http://www.billmate.se) This plugin utilizes Billmate Invoice, Billmate Bank, Billmate Card and Billmate Part Payment (Standard integration type).

When the order is passed to Billmate for Invoice and part payment, a credit record of the customer is made. If the check turns out all right, Billmate creates an invoice in Billmate Online. After you (as the merchant) completes the order in OpenCart, you need to log in to online.billmate.se to approve/send the invoice.

Billmate is a great payment alternative for merchants and customers in Sweden.


## COMPATIBILITY OpenCart versions
1.5.4 1.5.6 2.0 2.1 2.2 2.3

## Checkout Compatibility
* OpenCart default checkout 1.5.4 1.5.6 2.0 2.1 2.2 2.3
* Dreamvention Quickcheckout 4.5.1
* And more, feel free to contact us to discuss your checkout.

##INSTALLATION

1. Download and unzip the latest release zip file.

2. Make sure you have set permissions to edit our plugin in Admin. Permissions are found in System -> Users -> User Group.

# Attention OpenCart below 2.1
Before uploading the new plugin you will need to delete those folders:
* admin/controller/extension
* catalog/controller/extension

##KNOWN ISSUES
If currency setting is 0 decimal places, then it's better to use Myoc Price Rounding plugin for this.

# Important
This plugin will not work with php lower than PHP 5


## Changelog

### 2.2.5(2017-07-05)
* Fix - Rounding when coupon with free shipping and additional discount
* Enhancement - Add links to manuals in admin
* Enhancement - Disable autocomplete for pno inputs

### 2.2.4(2017-06-16)
* Fix - Phone number on order.
* Fix - Mixed content https.

### 2.2.2.2 (2017-03-02)
* Enhancement - Update readme for OpenCart 2.0.3.1

### 2.2.2.1 (2017-03-02)
* Fix - Legacy opencart total model paths.

### 2.2.2 (2017-03-01)
* Compatibility - Php 7.0
* Compatibility - Opencart 2.3

### 2.2.1 (2016-10-04)
* Fix - Order id in callback.
* Fix - Javascript in Admin

### 2.2.0 (2016-08-23)
* Fix - Opencart 2.2 compatibility. 
* Enhancement - Improved paymentplan logic in admin configurations for partpayment plugin. 

### 2.1.9 (2016-07-14)
* Enhancement - Improved messages in checkout when order is cancelled or failed for Cardpayment.

### 2.1.8 (2016-06-08)
* Fix - Get address Opencart 2.* Own template
* Fix - Fixes for Opencart 2.2 
* Enhancement - Partpayment default country/currency.

### 2.1.7 (2016-04-04)
* Fix - Partpayment on product page improved calculations.
* Fix - Bankpay order statuses Opencart 1.5
* Fix - Improved currencies for Invoice.
* Fix - Improved data sent to Api.


### 2.1.6(2016-01-26)
* Fix - Optimized Billmate.php

### 2.1.5 (2016-01-21)
* Fix - Rounding on totals.

### 2.1.4 (2016-01-13)
* Compatibility - Coupon compatibility for OpenCart 2.0
* Enhancement - Payment options design OpenCart 2.0


###2.1.3(2015-11-10)
* Fix - Install issue when country name was not in english.
* Enhancement - Consequent testmode text in admin.

###2.1.2(2015-10-14)
* Fix - Company checkout.
* Fix - Undefined logo error.

###2.1.1(2015-10-07)
* Fix - Plugin Version.
* Fix - More int 32 bit issues.

###2.1(2015-09-29)
* Enhancement - Added possibility to select logo for display on invoice.
* Fix - Int 32 bits problem.
* Fix - CURLOPT_SSL_VERIFYHOST error notice

###2.0.2.1(2015-09-27)
* Fix - Recover lost template files.

###2.0.2(2015-09-27)
* Enhancement - Better support for multi store and multi currency

###2.0.1(2015-09-15)
* Fix - Headers Already sent error
* Fix - Undefined variables partpayment cost ocmod

###2.0(2015-09-04)
55 commits and 41 issues closed

* Enhancement - Compatibility with Opencart 2.0.
* Enhancement - getAddress functionality.
* Enhancement - Show payment plans in Product/Category page.
* Enhancement - Improved compability with coupons.
* Enhancement - Improved compatibility with advanced coupons.
* Enhancement - Payment plans now in English and Swedish.
* Enhancement - Improved compatibility with Myoc Price Rounding plugin.
* Enhancement - Improved country restrictions settings for payment methods.
* Enhancement - Validation of your Billmate credentials upon saving.
* Enhancement - Improved vat calculation for coupons.
* Fix - Improved titles for payment method in orders.

