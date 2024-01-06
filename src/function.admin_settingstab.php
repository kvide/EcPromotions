<?php

# BEGIN_LICENSE
# -------------------------------------------------------------------------
# Module: EcPromotions (c) 2023 by CMS Made Simple Foundation
#
# An addon module to the EcOrderMgr/EcProductMgr/EcCart e-commerce
# suite to allow creating and managing promotions/sales in the
# order process.
#
# -------------------------------------------------------------------------
# A fork of:
#
# Module: Promotions (c) 2009-2018 by Robert Campbell
# (calguy1000@cmsmadesimple.org)
#
# -------------------------------------------------------------------------
#
# CMSMS - CMS Made Simple is (c) 2006 - 2023 by CMS Made Simple Foundation
# CMSMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit the CMSMS Homepage at: http://www.cmsmadesimple.org
#
# -------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple. You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
# -------------------------------------------------------------------------
# END_LICENSE
namespace EcPromotions;

if (! isset($gCms))
{
    exit();
}
if (! $this->CheckPermission('Modify Site Preferences'))
{
    exit();
}

//
// initialize
//
require_once (dirname(__FILE__) . '/lib/class.promotion.php');

//
// setup
//
$products_module = $this->GetModuleInstance('EcProductMgr');

//
// gather data
//

//
// give everything to smarty
//
$smarty->assign('currency_symbol', $products_module->GetPreference('product_currencysymbol', '$'));
$periods = array();
for ($i = 0; $i < 36; $i ++)
{
    $periods[$i + 1] = $i + 1;
}
$smarty->assign('promotion_periods', $periods);
$promotypes = array();
$promotypes[PROMOTIONS_OFFER_PERCENT] = $this->Lang(PROMOTIONS_OFFER_PERCENT);
$promotypes[PROMOTIONS_OFFER_DISCOUNT] = $this->Lang(PROMOTIONS_OFFER_DISCOUNT);
$promotypes[PROMOTIONS_OFFER_PRODUCT] = $this->Lang(PROMOTIONS_OFFER_PRODUCT);
$smarty->assign('offer_types', $promotypes);
$smarty->assign('dflt_promotion_period', $this->GetPreference('dflt_promotion_period', 1));
$smarty->assign('dflt_offer_type', $this->GetPreference('dflt_offer_type', PROMOTIONS_OFFER_PERCENT));
$smarty->assign('dflt_offer_data', $this->GetPreference('dflt_offer_data', '.1'));
$smarty->assign('image_dir', $this->GetPreference('image_dir', ''));
$smarty->assign('error_invalid_code', $this->GetPreference('error_invalid_code'));
$smarty->assign('error_empty_code', $this->GetPreference('error_empty_code'));
$smarty->assign('msg_valid_code', $this->GetPreference('msg_valid_code'));
$smarty->assign('formstart', $this->XTCreateFormStart($id, 'admin_savesettings'));
$smarty->assign('formend', $this->CreateFormEnd());

//
// process template
//
echo $this->ProcessTemplate('admin_settingstab.tpl');

#
# EOF
#
?>
