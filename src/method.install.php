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

// namespace EcPromotions;
if (! isset($gCms))
{
    exit();
}

// dummy object
$dummy = new \EcPromotions\promotion();

$db = &$this->GetDb();
$config = &$gCms->GetConfig();

$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

// Promotions table
$flds = "
         id I KEY AUTO,
         name C(255) KEY NOTNULL,
         description X,
         image C(255),
         item_order I NOTNULL,
         type C(20) NOTNULL,
         created " . CMS_ADODB_DT . ",
         start_date " . CMS_ADODB_DT . ",
         end_date " . CMS_ADODB_DT . ",
         offer_type  C(50) NOTNULL,
         offer_data  C(255) NOTNULL,
         extra X,
         owner I
        ";
$sqlarray = $dict->CreateTableSQL(\EcPromotions\PROMOTIONS_TABLE, $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// Conditions table
$flds = "
         id I KEY AUTO,
         promotion_id I KEY NOTNULL,
         cond_type C(50) NOTNULL,
         data X NOTNULL
        ";
$sqlarray = $dict->CreateTableSQL(\EcPromotions\PROMOTIONS_COND_TABLE, $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// permissions
$this->CreatePermission(\EcPromotions::MAIN_PERM, \EcPromotions::MAIN_PERM);

// preferences
$this->SetPreference('dflt_promotion_period', 1);
$this->SetPreference('dflt_offer_type', \EcPromotions\PROMOTIONS_OFFER_PERCENT);
$this->SetPreference('dflt_offer_data', '.1');
$this->SetPreference('image_dir', 'images');
$this->SetPreference('error_invalid_code', $this->Lang('error_invalid_code'));
$this->SetPreference('error_empty_code', $this->Lang('error_empty_code'));
$this->SetPreference('msg_valid_code', $this->Lang('msg_valid_code'));
$this->SetPreference('skurequired', 1);

// templates
# Setup default hierarchy report template
$fn = cms_join_path(dirname(__FILE__), 'templates', 'orig_couponform_template.tpl');
if (file_exists($fn))
{
    $template = file_get_contents($fn);
    $this->SetPreference(\EcPromotions::NEWCOUPONFORM_TEMPLATE, $template);
    $this->SetTemplate('couponform_Sample', $template);
    $this->SetPreference(\EcPromotions::DFLTCOUPONFORM_TEMPLATE, 'Sample');
}

#
# EOF
#
?>
