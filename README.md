# Billmate Payment Gateway for Magento
By Billmate AB - http://billmate.se/

Documentation with instructions on how to setup the plugin can be found at https://billmate.se/plugins/opencart/Instruktionsmanual_Opencart_Billmate_Plugin.pdf


## DESCRIPTION

Billmate Payment Gateway is a plugin that extends OpenCart, allowing your customers to get their products first and pay by invoice to Billmate later (http://www.billmate.se) This plugin utilizes Billmate Invoice, Billmate Bank, Billmate Card and Billmate Part Payment (Standard Integration type).

When the order is passed to Billmate for Invoice and part payment, a credit record of the customer is made. If the check turns out all right, Billmate creates an invoice in billmateonline. After you (as the merchant) completes the order in OpenCart, you need to log in to Billmateonline.se to approve/send the invoice.

Billmate is a great payment alternative for merchants and customers in Sweden.


##INSTALLATION

1. Download and unzip the latest release zip file.


##KNOWN ISSUES
If currency setting is 0 Decimal places, better use Myoc Price Rounding plugin for this. 


##Changelog

###2.0(2015-09-04)
50commits and 40 issues closed

* Enchancement - Compatibility with Opencart 2.0.
* Enchancement - getaddress functionality.
* Enchancement - Show Pay from on Product/Category page.
* Enchancement - Improved Compatibility with Coupons.
* Enchancement - Improved Compatibility with Advanced Coupons.
* Enchancement - Part payment plans now in English and Swedish.
* Enchancement - Improved Compatibility with Myoc Price Rounding plugin.
* Enchancement - Better Country Restrictions settings for payment methods.
* Enchancement - Validate your Billmate Credentials on Save.
* Enchancement - Improved vat calculation for Coupons.
* Fix - Payment method Titles for Orders improved.

