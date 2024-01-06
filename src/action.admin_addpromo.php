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
if (! $this->CheckPermission(\EcPromotions::MAIN_PERM))
{
    exit();
}

//
// initialization
//
$feu_module = $this->GetModuleInstance('MAMS');

//
// setup
//
$type = promotion::TYPE_CHECKOUT;
if (isset($params['type']))
{
    $type = trim($params['type']);
}

$promo = null;
if (isset($params['promoid']))
{
    // updating
    $promo = promo_ops::load_promo_by_id((int) $params['promoid']);
    $type = $promo->get_type(); // use the promotion type.
}
else
{
    // new promo
    $promo = new promotion();
    $promo->set_created(time());
    $promo->set_end_date(strtotime(sprintf('+%d months', $this->GetPreference('dflt_promotion_period'))));
    $promo->set_offer_type($this->GetPreference('dflt_offer_type'));
    $promo->set_offer_data($this->GetPreference('dflt_offer_data'));
}

// this stuff has to be after data loads so that the type is specified correctly.
switch ($type)
{
    case promotion::TYPE_INSTANT:
        $this->SetCurrentTab('instant_promotions');
        break;
    case promotion::TYPE_CHECKOUT:
        $this->SetCurrentTab('checkout_promotions');
        break;
}

if (isset($params['cancel']))
{
    $this->RedirectToTab($id);
}

//
// gather data
//
if (isset($params['submit']))
{
    // validate
    $error = '';

    $_get_data = function ($params, $idx)
    {
        $out = null;
        if (isset($params['saved_conddata0'][$idx]) && $params['saved_conddata0'][$idx])
        {
            $out[] = trim($params['saved_conddata0'][$idx]);
        }
        if (isset($params['saved_conddata1'][$idx]) && $params['saved_conddata1'][$idx])
        {
            $out[] = trim($params['saved_conddata1'][$idx]);
        }
        return $out;
    };

    // check to make sure that the name is valid.
    if (! promo_ops::check_unused_name(trim($params['name']), $promo->get_id()))
    {
        $error = $this->Lang('error_name_exists');
    }

    // build the promotion object
    $promo->set_name(trim($params['name']));
    if (isset($params['type']))
    {
        $promo->set_type($params['type']);
    }
    if (isset($params['description']))
    {
        $promo->set_description(trim($params['description']));
    }
    if (isset($params['image']))
    {
        $promo->set_image(trim($params['image']));
    }
    $sd = mktime(0, 0, 0, (int) $params['start_date_Month'], (int) $params['start_date_Day'],
                                        (int) $params['start_date_Year']);
    $promo->set_start_date($sd);
    $ed = mktime(0, 0, 0, (int) $params['end_date_Month'], (int) $params['end_date_Day'],
                                        (int) $params['end_date_Year']);
    $promo->set_end_date($ed);
    $promo->set_offer_type($params['offer_type']);
    $promo->set_offer_data(trim($params['offer_data']));
    $promo->del_conditions();
    if (isset($params['saved_condtype']))
    {
        for ($i = 0; $i < count($params['saved_condtype']); $i ++)
        {
            $cond = new promotion_condition();
            $cond->set_cond_type($params['saved_condtype'][$i]);
            $cond->set_data($_get_data($params, $i));
            $promo->add_condition($cond);
        }
    }

    $promo->clear_extra();
    foreach ($params as $key => $value)
    {
        if (! startswith($key, 'extra_'))
        {
            continue;
        }
        $nkey = substr($key, 6);
        $promo->set_extra($nkey, $value);
    }

    // validate the conditions.
    if (empty($error) && isset($params['saved_condtype']))
    {
        for ($i = 0; $i < count($params['saved_condtype']); $i ++)
        {
            if (! empty($error))
            {
                break;
            }

            $data = $_get_data($params, $i);
            switch ($params['saved_condtype'][$i])
            {
                case PROMOTIONS_COND_FEU:
                    if (! $feu_module)
                    {
                        $error = $this->Lang('error_module_not_found', 'MAMS');
                    }
                    else if (count($data) != 1)
                    {
                        $error = $this->Lang('error_invalid_feu_group');
                    }
                    else
                    {
                        if (! $feu_module->GroupExistsByName(trim($data[0])))
                        {
                            $error = $this->Lang('error_invalid_feu_group');
                            break;
                        }
                    }
                    break;

                case PROMOTIONS_COND_SUBTOTAL:
                case PROMOTIONS_COND_WEIGHT:
                    if (count($data) != 1 || (float) $data[0] < 0.00001)
                    {
                        $error = $this->Lang('error_invalid_order_amount');
                    }
                    else if ($type == promotion::TYPE_INSTANT)
                    {
                        $error = $this->Lang('error_invalid_condition_type');
                    }
                    else
                    {
                        $tmp = floatval($data);
                        if ($tmp <= 0.01 || $tmp > 100000)
                        {
                            $error = $this->Lang('error_invalid_order_amount');
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODSKU:
                    // todo
                    break;

                case PROMOTIONS_COND_PRODID:
                    if (count($tmp) != 1)
                    {
                        $error = $this->Lang('error_invalid_product');
                    }
                    else
                    {
                        $list = explode(',', $data);
                        foreach ($list as $tmp)
                        {
                            if (! \EcProductMgr\product_ops::is_valid_product_id(trim($tmp)))
                            {
                                $error = $this->Lang('error_invalid_product');
                                break;
                            }
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODCAT:
                    if (count($data) != 1)
                    {
                        $error = $this->Lang('error_invalid_product_category');
                    }
                    else
                    {
                        $list = explode(',', $data);
                        foreach ($list as $tmp)
                        {
                            if (! \EcProductMgr\product_ops::is_valid_category(trim($tmp)))
                            {
                                $error = $this->Lang('error_invalid_product_category');
                                break;
                            }
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODHIER:
                    // todo
                    break;

                case PROMOTIONS_COND_COUPON:
                    if (count($data) != 1)
                    {
                        $error = $this->Lang('error_coupon_exists');
                    }
                    else if (! promo_ops::check_unused_coupon($data[0], $promo->get_id()))
                    {
                        $error = $this->Lang('error_coupon_exists');
                    }
                    break;

                case PROMOTIONS_COND_BULKPURCHASE:
                    if (count($data) != 2 || $data[0] == '' || (int) $data[1] < 1)
                    {
                        $error = $this->Lang('error_invalid_bulkpurchase');
                    }
                    // todo: verify sku (may have to ask for source)
                    break;
            } // switch
        } // for each condition

        // validate the offer
        if (empty($error))
        {
            switch ($params['offer_type'])
            {
                case PROMOTIONS_OFFER_PERCENT:
                    if ($type == promotion::TYPE_INSTANT)
                    {
                        $error = $this->Lang('error_invalid_offer_type');
                    }
                    else
                    {
                        $tmp = floatval($params['offer_data']);
                        if ($tmp < 0.0001 || $tmp > 100.0)
                        {
                            $error = $this->Lang('error_invalid_value');
                        }
                    }
                    break;

                case PROMOTIONS_OFFER_PRODDISCOUNT:
                    $tmp = floatval($params['offer_data']);
                    if ($tmp < 0.0001 || $tmp > 100.0)
                    {
                        $error = $this->Lang('error_invalid_value');
                    }
                    break;

                case PROMOTIONS_OFFER_DISCOUNT:
                    if ($type == promotion::TYPE_INSTANT)
                    {
                        $error = $this->Lang('error_invalid_offer_type');
                    }
                    else
                    {
                        $tmp = floatval($params['offer_data']);
                        if ($tmp < 0.0001 || $tmp > 10000.0)
                        {
                            $error = $this->Lang('error_invalid_value');
                        }
                    }
                    break;

                case PROMOTIONS_OFFER_PRODUCT:
                    $tmp = trim($params['offer_data']);
                    if (! \EcProductMgr\product_ops::is_valid_product_id($tmp))
                    {
                        $error = $this->Lang('error_invalid_product');
                    }
                    break;
            }
        }

        // put the params in the promo object
        if (empty($error))
        {

            // commit
            $res = $promo->save();
            if ($res)
            {
                $this->RedirectToTab($id);
            }
            $error = $this->Lang('error_save_promotion');
        }

        if ($error)
        {
            echo $this->ShowErrors($error);
        }
    }
}

//
// give everything to smarty
//
$tmp = promotion::get_types_array();
$promotypes = array();
foreach ($tmp as $one)
{
    $promotypes[$one] = $this->Lang($one);
}
$smarty->assign('promotypes', $promotypes);

$tmp = promotion::get_offer_array($type);
$offertypes = array();
foreach ($tmp as $one)
{
    $offertypes[$one] = $this->Lang($one);
}
$smarty->assign('offertypes', $offertypes);

$tmp = promotion_condition::get_types_array($type);
$condtypes = array();
foreach ($tmp as $cond)
{
    if (! $feu_module && $cond == PROMOTIONS_COND_FEU)
    {
        continue;
    }

    $rec = array('name' => $cond,'label' => $this->Lang($cond));
    $rec['fields'] = array($this->Lang('promptdata_' . $cond));
    if ($cond == PROMOTIONS_COND_BULKPURCHASE)
    {
        $rec['fields'][] = $this->Lang('promptdata_' . $cond . '2');
    }
    $condtypes[$cond] = $rec;
}

$smarty->assign('conditiontypes', $condtypes);
$smarty->assign('formstart', $this->XTCreateFormStart($id, 'admin_addpromo', $returnid, $params));
$smarty->assign('formend', $this->CreateFormEnd());
$smarty->assign('promo', $promo);
$smarty->assign('promotype', $type);
if ($promo->count_conditions())
{
    $smarty->assign('conditions', $promo->get_conditions());
}
// $exts = \xt_utils::get_image_extensions();
$dir = $this->GetPreference('imagedir', $config['image_uploads_path']);
// $files = \xt_dir::get_file_list($dir,$exts);
$files = \CMSMSExt\FS::get_file_list($dir, 'gif, png');
$files = \xt_array::hash_prepend($files, '-1', $this->Lang('none'));
$smarty->assign('file_list', $files);

//
// Process template
//
echo $this->ProcessTemplate('admin_addpromo.tpl');

#
# EOF
#
?>
