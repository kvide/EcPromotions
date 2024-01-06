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

use \EcommerceExt\setup_manager;

if (! isset($gCms))
{
    exit();
}
if (! $this->CheckPermission('Modify Site Preferences'))
{
    exit();
}

if (\xt_param::exists($params, 'setaspromo'))
{
    try
    {
        setup_manager::set_promotion_assistant($this->GetName());
        $this->SetMessage($this->Lang('msg_setaspromo'));
        $this->RedirectToTab();
    }
    catch (\Exception $e)
    {
        $this->SetError($e->GetMessage());
        $this->RedirectToTab();
    }
}

$this->SetCurrentTab('settings');
$this->SetPreference('dflt_promotion_period', (int) $params['promotion_period']);
$this->SetPreference('dflt_offer_type', trim($params['offer_type']));
$this->SetPreference('image_dir', trim($params['image_dir']));
$this->SetPreference('error_invalid_code', trim($params['error_invalid']));
$this->SetPreference('error_empty_code', trim($params['error_empty']));
$this->SetPreference('msg_valid_code', trim($params['msg_valid']));

switch ($params['offer_type'])
{
    case PROMOTIONS_OFFER_PERCENT:
        if (empty($params['offer_percent']) || ($params['offer_percent'] < 0.001) || ($params['offer_percent'] > 100.0))
        {
            $this->SetError($this->Lang('error_invalid_value'));
            $this->RedirectToTab($id);
        }
        $this->SetPreference('dflt_offer_data', $params['offer_percent']);
        break;
    case PROMOTIONS_OFFER_DISCOUNT:
        if (empty($params['offer_price']) || ($params['offer_price'] < 0.001))
        {
            $this->SetError($this->Lang('error_invalid_value'));
            $this->RedirectToTab($id);
        }
        $this->SetPreference('dflt_offer_data', $params['offer_price']);
        break;
    default:
        $this->SetPreference('dflt_offer_data', '');
        break;
}
$this->SetMessage($this->Lang('msg_prefsupdated'));

$this->RedirectToTab($id);

#
# EOF
#
?>
