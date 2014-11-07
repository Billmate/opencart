opencart
========

Billmate payment plugin for opencart

Troubleshooting.

1. If customer cant get to checkout/success when using Billmate_invoice or Billmate_partpayment.
Check in Opencart Backend in System -> Localization -> Countries that available 
countries is enabled. For example if customer may checkout from Sweden, you have to enable that.

In the same time you can check that Country name is Sweden or else you will have to find 

$countriesdata = array(209 =>'sweden', 73=> 'finland',59=> 'denmark', 164 => 'norway', 81 => 'germany', 15 => 'austria', 154 => 'netherlands' );

and change to appropriate name.

For example if You have named Sweden to Sverige in backend you have to change:
$countriesdata = array(209 =>'sverige', 73=> 'finland',59=> 'denmark', 164 => 'norway', 81 => 'germany', 15 => 'austria', 154 => 'netherlands' );