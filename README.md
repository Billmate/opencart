# Billmate Payment Gateway for Opencart
By Billmate AB - http://billmate.se/

Documentation with instructions on how to setup the plugin can be found at https://billmate.se/plugins/manual/Installation_Manual_Opencart_Billmate.pdf


## DESCRIPTION

Billmate Payment Gateway is a plugin that extends OpenCart, allowing your customers to get their products first and pay by invoice to Billmate later (http://www.billmate.se) This plugin utilizes Billmate Invoice, Billmate Bank, Billmate Card and Billmate Part Payment (Standard integration type).

When the order is passed to Billmate for Invoice and part payment, a credit record of the customer is made. If the check turns out all right, Billmate creates an invoice in Billmate Online. After you (as the merchant) completes the order in OpenCart, you need to log in to online.billmate.se to approve/send the invoice.

Billmate is a great payment alternative for merchants and customers in Sweden.


##INSTALLATION

1. Download and unzip the latest release zip file.


##KNOWN ISSUES
If currency setting is 0 decimal places, then it's better to use Myoc Price Rounding plugin for this.


##Changelog

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

