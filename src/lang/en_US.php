<?php

#A
$lang['add'] = 'Add';


#B


#C
$lang['cancel'] = 'Cancel';
$lang['clear_coupons'] = 'Clear Coupons';
$lang['coupons_cleared'] = 'We have reset all of your coupons';
$lang['couponformtemplate_addedit'] = 'Add or Edit a Coupon Form Template';


#D
$lang['delete'] = 'Delete';
$lang['default_template_notice'] = <<<EOT
<strong>Note:</strong> The contents of these text areas are used to determine the default content of templates when you click &quot;Add Template&quot; in the appropriate template tab.  Editing one of these text areas will have no immediate effect on your website.
EOT;


#E
$lang['edit'] = 'Edit';
$lang['entered_coupons'] = 'Coupons you have entered:';
$lang['error_already_prod'] = 'A product condition already exists in the condition list';
$lang['error_coupon_exists'] = 'A promotion using that coupon string already exists';
$lang['error_duplicate_condition'] = 'A duplicate condition already exists in the condition list';
$lang['error_duplicate_type'] = 'A duplicate product condition already exists';
$lang['error_empty_code'] = 'Please enter a valid value';
$lang['error_invalid_bulkpurchase'] = 'The bulk purchase data specified is invalid';
$lang['error_invalid_code'] = 'Promotion Code not recognised';
$lang['error_invalid_condition_type'] = 'A condition specified is invalid for this promotion type';
$lang['error_invalid_feu_group'] = 'Invalid FEU Group Name Specified';
$lang['error_invalid_order_amount'] = 'The order amount specified is invalid';
$lang['error_invalid_offer_type'] = 'The offer specified is invalid for this type of order';
$lang['error_invalid_product_category'] = 'At least one invalid product category';
$lang['error_invalid_product'] = 'Invalid product id specified';
$lang['error_invalid_value'] = 'Invalid Value Specified';
$lang['error_missing_param'] = 'A required input parameter is missing';
$lang['error_module_not_found'] = 'A Required Module (%s) Could not be found';
$lang['error_name_exists'] = 'A promotion by that name already exists';
$lang['error_nooffer_data'] = 'There is no value associated with the offer type';
$lang['error_promotion_name'] = 'Each promotion must have a name';
$lang['error_save_promotion'] = 'An error occurred trying to save this promotion';
$lang['error_specify_value'] = 'Please specify a value';

#F
$lang['friendlyname'] = 'Promotion Manager';

#G


#H

#I
$lang['info_checkout_promotions'] = 'Checkout promotions are calculated when the order is checked out.  Only the first matching promotion will be used.  No already discounted cart items will be matched.';
$lang['info_conditions'] = 'Specify one or more sales conditions for this promotion';
$lang['info_condition_data'] = 'Provide the appropriate data for this condition';
$lang['info_extras'] = 'This area contains optional additional attributes for each promotion for use when displaying promotion banners or promotion information.';
$lang['info_instant_promotions'] = 'Instant promotions are calculated when the item is added to the cart.  Attempts are made to display potential discounts before checkout.  The first promotion that matches the selected cart item will be used in calculating discounts.';
$lang['info_image_dir'] = 'Specify a directory name relative to the uploads directory in which to search for images to attach to offers';
$lang['info_promo_name'] = 'This name is used for invoices and sales orders to describe the discount being applied';
$lang['info_promo_type'] = 'Select the appropriate type for this promotion.  Note that offers that effect the order total will have no effect in &quot;instant&quot; type promotions.';
$lang['infodata_PROMOTIONS_COND_FEU'] = 'A comma separated list of FEU group names is permitted';
$lang['infodata_PROMOTIONS_COND_SUBTOTAL'] = 'Enter a floating point value using a . for a decimal separator.  i.e:  100.00';
$lang['infodata_PROMOTIONS_COND_WEIGHT'] = 'Enter a floating point value using a . for a decimal separator.  i.e:  100.00';
$lang['infodata_PROMOTIONS_COND_COUPON'] = 'Enter a text string of no more than 10 characters';
$lang['infodata_PROMOTIONS_COND_PRODID'] = 'Enter a comma separated list of product ids';
$lang['infodata_PROMOTIONS_COND_PRODSKU'] = 'Enter a comma separated list of product SKU\'s';
$lang['infodata_PROMOTIONS_COND_PRODCAT'] = 'Enter a comma separated list of product category names';
$lang['infodata_PROMOTIONS_COND_PRODHIER'] = 'Enter a single product hierarchy name in the format &quot;grandparent.parent.child&quot;';
$lang['infodata_PROMOTIONS_COND_BULKPURCHASE'] = 'Enter a minumum quantity and a sku';

#J


#K


#L
$lang['lbl_add_promo'] = 'Add Promotion';
$lang['lbl_edit_promo'] = 'Edit Promotion';
$lang['lbl_checkout_promotions'] = 'Checkout Promotions';
$lang['lbl_instant_promotions'] = 'Instant Promotions';
$lang['lbl_promotions'] = 'Promotions';
$lang['lbl_settings'] = 'Settings';
$lang['lbl_couponform_templates'] = 'Coupon Form Templates';
$lang['lbl_default_templates'] = 'Default Templates';
$lang['lbl_onlyone'] = 'Allow only one &quot;free&quot; item';
$lang['lbl_promo_messages'] = 'Promotion Messages';

#M
$lang['moddescription'] = 'Create, manage, and interact with sales and promotions for your e-commerce site';
$lang['msg_prefsupdated'] = 'Preferences Updated';
$lang['msg_promodeleted'] = 'Promotion Deleted';
$lang['msg_setaspromo'] = 'Promotion module set';
$lang['msg_valid_code'] = 'Your valid promotion code was recognised!';

#N
$lang['none'] = 'None';

#O


#P
$lang['param_inline'] = 'Specify that the coupon code form is inline, and the results should replace the original tag and not the page contents';
$lang['param_template'] = 'Specify the name of a non default coupon code template to display for the default action';
$lang['postinstall'] = 'The Promotions module has been installed, please ensure that the authorized users have the &quot;Manage Promotions&quot; permission, and then configure your promotions and templates appropriately';
$lang['postuninstall'] = 'The Promotions module and all applicable data has been uninstalled from the system.  You may now remove the files associated with this module';
$lang['PROMOTIONS_COND_COUPON'] = 'Require a Coupon Code';
$lang['PROMOTIONS_COND_FEU'] = 'Require membership in an FEU group';
$lang['PROMOTIONS_COND_SUBTOTAL'] = 'Require a minimum purchase amount';
$lang['PROMOTIONS_COND_WEIGHT'] = 'Require a minimum order weight';
$lang['PROMOTIONS_COND_PRODID'] = 'Require purchase of a specific product';
$lang['PROMOTIONS_COND_PRODCAT'] = 'Require purchase of at least one product in a category';
$lang['PROMOTIONS_COND_PRODHIER'] = 'Require purchase of at least one product in a hierarchy';
$lang['PROMOTIONS_COND_PRODSKU'] = 'Require purchase of a specific product (by SKU)';
$lang['PROMOTIONS_COND_BULKPURCHASE'] = 'Require a purchase of a minumum quantity of a specific product (by SKU)';
$lang['01_PROMOTIONS_OFFER_PRODUCT'] = 'Receive a free product';
$lang['02_PROMOTIONS_OFFER_PRODDISCOUNT'] = 'Discount the applicable products by a percentage';
$lang['03_PROMOTIONS_OFFER_PERCENT'] = 'Percentage off order total';
$lang['04_PROMOTIONS_OFFER_DISCOUNT'] = 'Reduce order total by a specified dollar figure';
$lang['05_PROMOTIONS_OFFER_SAMEPRODUCT'] = 'Get the same product free';
$lang['06_PROMOTIONS_OFFER_PRODUCTSKU'] = 'Receive a free product (by SKU)';
$lang['07_PROMOTIONS_OFFER_PRODAMOUNT'] = 'Discount the applicable products by a fixed amount';
$lang['promo_type_instant'] = 'Promotion offer calculated in the Cart';
$lang['promo_type_checkout'] = 'Promotion offer calculated on Checkout';
$lang['prompt_add_condition'] = 'Add a Condition';
$lang['prompt_addpromo'] = 'Add a New Promotion';
$lang['prompt_allow_cumulative'] = 'Apply this promotion to already discounted items?';
$lang['prompt_allow_once'] = 'This promotion can only be used once per transaction';
$lang['prompt_code'] = 'Coupon Code';
$lang['prompt_condition_data'] = 'Condition Data';
$lang['prompt_condition_type'] = 'Type of Condition';
$lang['prompt_conditions'] = 'Conditions';
$lang['prompt_coupon_code'] = 'Enter a Coupon or Promotion Code';
$lang['prompt_dflt_offer_type'] = 'Default offer type';
$lang['prompt_dflt_promotion_period'] = 'Default Promotion Period (months)';
$lang['prompt_end_date'] = 'End Date';
$lang['prompt_error_empty'] = 'Empty Code Message';
$lang['prompt_error_invalid'] = 'Invalid Code Message';
$lang['prompt_extras'] = 'Extras';
$lang['prompt_free_product'] = 'Specify the free product';
$lang['prompt_id'] = 'ID';
$lang['prompt_image'] = 'Image';
$lang['prompt_image_dir'] = 'Image Directory';
$lang['prompt_description'] = 'Description';
$lang['prompt_msg_valid_code'] = 'Valid Code Message';
$lang['prompt_name'] = 'Name';
$lang['prompt_offer'] = 'Offer';
$lang['prompt_offer_data'] = 'Offer Data';
$lang['prompt_offer_type'] = 'Offer Type';
$lang['prompt_offers'] = 'Offers';
$lang['prompt_percentage_discount'] = 'Percent Discount <em>(expressed as a decimal from 0 to 1)</em>';
$lang['prompt_price_discount'] = 'Specify the dollar figure to remove from the order';
$lang['prompt_start_date'] = 'Start Date';
$lang['prompt_template'] = 'Template';
$lang['prompt_type'] = 'Type';
$lang['promptdata_PROMOTIONS_COND_COUPON'] = 'Coupon Code';
$lang['promptdata_PROMOTIONS_COND_FEU'] = 'FEU Group Name';
$lang['promptdata_PROMOTIONS_COND_SUBTOTAL'] = 'Minimum Order Amount';
$lang['promptdata_PROMOTIONS_COND_WEIGHT'] = 'Minimum Order Weight';
$lang['promptdata_PROMOTIONS_COND_PRODID'] = 'Product Id';
$lang['promptdata_PROMOTIONS_COND_PRODSKU'] = 'Product SKU';
$lang['promptdata_PROMOTIONS_COND_PRODCAT'] = 'Product Category';
$lang['promptdata_PROMOTIONS_COND_PRODHIER'] = 'Product Hierarchy';
$lang['promptdata_PROMOTIONS_COND_BULKPURCHASE'] = 'Product SKU';
$lang['promptdata_PROMOTIONS_COND_BULKPURCHASE2'] = 'Minimum Quantity';
$lang['promptoffer_03_PROMOTIONS_OFFER_PERCENT'] = 'Discount Percentage <em>(expressed as a value from 0.00 to 1.00)</em>';
$lang['promptoffer_04_PROMOTIONS_OFFER_DISCOUNT'] = 'Cash Discount';
$lang['promptoffer_01_PROMOTIONS_OFFER_PRODUCT'] = 'Product ID to give free';
$lang['promptoffer_02_PROMOTIONS_OFFER_PRODDISCOUNT'] = 'Discount Percentage <em>(expressed as a value from 0.00 to 1.00)</em>';
$lang['promptoffer_05_PROMOTIONS_OFFER_SAMEPRODUCT'] = 'Offer the same product free <em>(any value here is valid)</em>';
$lang['promptoffer_06_PROMOTIONS_OFFER_PRODUCTSKU'] = 'Product SKU(s) that are free with the order (only the first matching item in the cart will be discounted)';
$lang['promptoffer_07_PROMOTIONS_OFFER_PRODAMOUNT'] = 'Discount amount <em>(expressed as a value, cannot exceed the products price.</em>';

#Q


#R
$lang['really_delete_promotion'] = 'Are you sure you want to remove this promotion?';
$lang['really_uninstall'] = 'Are you sure you want to uninstall this module? Proceeding will permanently remove all of your promotions data and may cause problems with a working e-commerce website.';
$lang['resettofactory'] = 'Reset To Factory Defaults';

#S
$lang['setaspromo'] = 'Set promotion module';
$lang['submit'] = 'Submit';

#T
$lang['title_couponform_dflttemplate'] = 'System Default Coupon Form Template';
$lang['type'] = 'Type';

#U


#V


#W
$lang['warn_instant_promotions'] = 'Instant promotions will not work with when using the &quot;Cart&quot; module.';
$lang['warning_no_conditions'] = 'You have not specified any conditions for this offer (other than a start and end date).  Click OK to continue, or Cancel to correct this';

#X


#Y


#Z


?>
