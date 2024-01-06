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

//
// initialization
//
$thetemplate = $this->GetPreference(\EcPromotions::DFLTCOUPONFORM_TEMPLATE);
$code = '';
$error = '';
$message = '';
$sess = new \xt_session($this->GetName());

//
// handle params
//
$inline = \xt_param::get_bool($params, 'inline');
$thetemplate = \xt_param::get_string($params, 'template', $thetemplate);

//
// hande form data
//
if (isset($params['promo_submit']))
{
    if (isset($params['promo_code']))
    {
        $code = trim($params['promo_code']);
    }

    $error = '';
    $msg = '';
    if ($code == '')
    {
        $error = $this->GetPreference('error_empty_code');
    }
    else
    {
        // check for code validity
        if (! promo_ops::is_valid_coupon($code))
        {
            $error = $this->GetPreference('error_invalid_code');
        }
        else
        {
            \EcPromotions::add_coupon($code);
            $msg = $this->GetPreference('msg_valid_code');
            $code = '';
        }
    }

    if ($error)
    {
        $sess->put('error', $error);
    }
    if ($msg)
    {
        $sess->put('msg', $msg);
    }

    if (isset($params['promo_orig_url']))
    {
        $url = trim($params['promo_orig_url']);
        $url = str_replace('amp;', '', $url);
        if ($url)
        {
            redirect($url);
        }
    }
}
elseif (isset($params['promo_clear']))
{
    \EcPromotions::clear_coupons();
    $msg = $this->Lang('coupons_cleared');

    $sess->put('msg', $msg);
}

//
// give everything to smarty
//
if ($sess->exists('msg'))
{
    $msg = $sess->get('msg');
    $sess->clear('msg');
}
if ($sess->exists('error'))
{
    $error = $sess->get('error');
    $sess->clear('error');
}
if ($sess->exists('coupons'))
{
    $coupons = $sess->get('coupons');
    if (is_array($coupons) && count($coupons))
    {
        $smarty->assign('coupons', $coupons);
    }
}

if (isset($msg) && $msg != '')
{
    $smarty->assign('msg', $msg);
}
if (isset($error) && $error != '')
{
    $smarty->assign('error', $error);
}
if (! isset($params['promo_orig_url']))
{
    $params['promo_orig_url'] = \xt_url::current_url();
}

$smarty->assign('code', $code);
$smarty->assign('formstart', $this->XTCreateFormStart($id, 'default', $returnid, $params, $inline));
$smarty->assign('formend', $this->CreateFormEnd());

//
// output
//
echo $this->ProcessTemplateFromDatabase('couponform_' . $thetemplate);

#
# EOF
#
?>
