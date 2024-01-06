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

if (! class_exists('\EcommerceExt\EcommModule'))
{
    $mod = \cms_utils::get_module('EcommerceExt');
    $mod->autoload('EcommModule');
}

use \EcPromotions\promotion;

define('EcPromotions\PROMOTIONS_TABLE', cms_db_prefix() . 'module_ec_promotions');
define('EcPromotions\PROMOTIONS_COND_TABLE', cms_db_prefix() . 'module_ec_promotions_cond');
define('EcPromotions\PROMOTIONS_COND_FEU', 'PROMOTIONS_COND_FEU'); // FEU group membership
define('EcPromotions\PROMOTIONS_COND_SUBTOTAL', 'PROMOTIONS_COND_SUBTOTAL'); // Order total (pre shipping and taxes)
define('EcPromotions\PROMOTIONS_COND_WEIGHT', 'PROMOTIONS_COND_WEIGHT'); // Order weight
define('EcPromotions\PROMOTIONS_COND_PRODID', 'PROMOTIONS_COND_PRODID'); // Order has a certain product id
define('EcPromotions\PROMOTIONS_COND_PRODCAT', 'PROMOTIONS_COND_PRODCAT'); // Order has a product from a certain category
define('EcPromotions\PROMOTIONS_COND_PRODHIER', 'PROMOTIONS_COND_PRODHIER'); // Order has a product from a certain hierarchy
define('EcPromotions\PROMOTIONS_COND_PRODSKU', 'PROMOTIONS_COND_PRODSKU'); // Order has a certain product sku
define('EcPromotions\PROMOTIONS_COND_COUPON', 'PROMOTIONS_COND_COUPON'); // Order has a product from a certain hierarchy
define('EcPromotions\PROMOTIONS_COND_BULKPURCHASE', 'PROMOTIONS_COND_BULKPURCHASE'); // Order has a minumum purchase of N quantity of specific SKU
define('EcPromotions\PROMOTIONS_OFFER_PRODUCT', '01_PROMOTIONS_OFFER_PRODUCT');
define('EcPromotions\PROMOTIONS_OFFER_PRODDISCOUNT', '02_PROMOTIONS_OFFER_PRODDISCOUNT');
define('EcPromotions\PROMOTIONS_OFFER_PERCENT', '03_PROMOTIONS_OFFER_PERCENT');
define('EcPromotions\PROMOTIONS_OFFER_DISCOUNT', '04_PROMOTIONS_OFFER_DISCOUNT');
define('EcPromotions\PROMOTIONS_OFFER_SAMEPRODUCT', '05_PROMOTIONS_OFFER_SAMEPRODUCT');
define('EcPromotions\PROMOTIONS_OFFER_PRODUCTSKU', '06_PROMOTIONS_OFFER_PRODUCTSKU');
define('EcPromotions\PROMOTIONS_OFFER_PRODAMOUNT', '\07_PROMOTIONS_OFFER_PRODAMOUNT');

class EcPromotions extends \EcommerceExt\EcommModule
{
    const NEWCOUPONFORM_TEMPLATE = 'NEWCOUPONFORM_TEMPLATE';
    const DFLTCOUPONFORM_TEMPLATE = 'DFLTCOUPONFORM_TEMPLATE';
    const MAIN_PERM = 'Manage Promotions';

    private $_helper;

    public function __construct()
    {
        parent::__construct();

        $smarty = cmsms()->GetSmarty();
        if (! $smarty)
        {
            return;
        }
        $smarty->register_function('promo_get_prod_discount', array(
            'EcPromotions',
            'smarty_function_promo_get_prod_discount'
        ));
    }

    public function GetFriendlyName()
    {
        return $this->Lang('friendlyname');
    }

    public function GetVersion()
    {
        return '0.98.0';
    }

    public function MinimumCMSVersion()
    {
        return '2.2.19';
    }

    public function GetAuthor()
    {
        return 'Christian Kvikant';
    }

    public function IsPluginModule()
    {
        return true;
    }

    public function GetAuthorEmail()
    {
        return 'kvide@kvikant.fi';
    }

    public function HasAdmin()
    {
        return true;
    }

    public function GetAdminSection()
    {
        return 'ecommerce';
    }

    public function GetAdminDescription()
    {
        return $this->Lang('moddescription');
    }

    public function LazyLoadAdmin()
    {
        return TRUE;
    }

    public function LazyLoadFrontend()
    {
        return FALSE;
    }

    public function VisibleToAdminUser()
    {
        return $this->CheckPermission(\EcPromotions::MAIN_PERM)
               || $this->CheckPermission('Modify Templates')
               || $this->CheckPermission('Modify Site Preferences');
    }

    public function GetDependencies()
    {
        return array(
            'CMSMSExt' => '1.4.5',
            'EcommerceExt' => '0.98.0',
            'EcOrderMgr' => '0.98.0',
            'EcProductMgr' => '0.98.0'
        );
    }

    public function InstallPostMessage()
    {
        return $this->Lang('postinstall');
    }

    public function UninstallPostMessage()
    {
        return $this->Lang('postuninstall');
    }

    public function UninstallPreMessage()
    {
        return $this->Lang('really_uninstall');
    }

    public function AllowAutoInstall()
    {
        return FALSE;
    }

    public function AllowAutoUpgrade()
    {
        return FALSE;
    }

    public function SetParameters()
    {
        $this->AddImageDir('icons');
        $this->RegisterModulePlugin();
        $this->RestrictUnknownParams();

        $this->CreateParameter('inline', 'default', $this->Lang('param_inline'));
        $this->SetParameterType('inline', CLEAN_INT);

        $this->CreateParameter('template', '', $this->Lang('param_template'));
        $this->SetParameterType('template', CLEAN_STRING);

        $this->SetParameterType(CLEAN_REGEXP . '/promo_.*/', CLEAN_STRING);
    }

    public function GetHeaderHTML()
    {
        return;
        $txt = <<<EOT
<style type="text/css">
fieldset.xtfieldset {
  margin-left: 20%;
}
legend.xtlegend {
  font-size: 1em;
  font-weight: normal;
}
.nooffset {
  margin-left: 0;
}
</style>
EOT;
        $obj = $this->GetModuleInstance('JQueryTools');
        if (is_object($obj))
        {
            $tmpl = <<<EOT
{JQueryTools action='incjs' exclude='form'}
{JQueryTools action='ready'}
EOT;
            // $txt .= $this->ProcessTemplateFromData($tmpl);
        }

        return $txt;
    }

    public function HasCapability($capability, $params = array())
    {
        if ($capability == 'promotions_calculation')
        {
            return TRUE;
        }

        return parent::HasCapability($capability, $params);
    }

    /**
     * Find applicable promotions given basket details, and other information
     *
     * @param
     *            int UserID from FEU module
     * @param
     *            array Array of items in the basket (array of objects)
     * @param
     *            float subtotal of order (before taxes or shipping)
     * @param
     *            float weight of order (products has the units)
     * @param
     *            int unix format date for which order is applicable
     * @returns hash of promotion name, and amount of promotion.  promotion with higher discount wins.
     * @deprecated
     */
    public function FindPromotions($feu_uid, $items, $subtotal, $weight, $date = '')
    {
        // this function could use promotion tester... but
        // it expects that the items are cart items.
        if (empty($date))
        {
            $date = time();
        }
        $sess = new \xt_session('EcPromotions');
        $db = $this->GetDb();
        $date = $db->DbTimeStamp($date);

        // now we have a list of promotion id's load each promotion
        // and test it.
        // calculate the discounts applied for all line items
        // and calculate the value of them.
        $promos = promotion::load_all_by_type(promotion::TYPE_CHECKOUT);
        $discount_amt = 10000000;
        $discount_items = '';
        foreach ($promos as $promo)
        {
            $match_type = '';

            // check all of the conditions in the promotion
            $match = true;
            for ($i = 0; $i < $promo->count_conditions(); $i ++)
            {
                $condition = $promo->get_condition($i);

                switch ($condition->get_cond_type())
                {
                    case PROMOTIONS_COND_FEU:
                        if (! \EcPromotions\promo_tests::test_feu_uid($feu_uid, $promo))
                        {
                            $match = false;
                            break;
                        }
                        break;

                    case PROMOTIONS_COND_SUBTOTAL:
                        if (! \EcPromotions\promo_tests::test_order_subtotal($subtotal, $promo))
                        {
                            $match = false;
                            break;
                        }
                        break;

                    case PROMOTIONS_COND_COUPON:
                        $coupons = $sess->get('coupons', '');
                        if ($coupons)
                        {
                            // test all of our coupons.
                            $test = false;
                            foreach ($coupons as $coupon)
                            {
                                if (\EcPromotions\promo_tests::test_coupon($coupon, $promo))
                                {
                                    $test = true;
                                    break;
                                }
                            }
                            if (! $test)
                            {
                                $match = false;
                            }
                        }
                        else
                        {
                            $match = false;
                        }
                        break;

                    case PROMOTIONS_COND_WEIGHT:
                        if (! \EcPromotions\promo_tests::test_order_weight($weight, $promo))
                        {
                            $match = false;
                        }
                        break;

                    case PROMOTIONS_COND_PRODID:
                        if (! \EcPromotions\promo_tests::test_single_product($items, $promo))
                        {
                            $match = false;
                        }
                        break;

                    case PROMOTIONS_COND_PRODCAT:
                        if (! \EcPromotions\promo_tests::test_product_from_category($items, $promo))
                        {
                            $match = false;
                        }
                        break;

                    case PROMOTIONS_COND_PRODHIER:
                        if (! \EcPromotions\promo_tests::test_product_from_hierarchy($items, $promo))
                        {
                            $match = false;
                        }
                        break;
                }

                // if one condition does not match, the test fails.
                if (! $match)
                {
                    break;
                }
            }

            // check the value of the discount
            if ($match)
            {
                $adj_items = \EcPromotions\promo_ops::calculate_discount($promo, $subtotal, $items);
                $adj_amt = \EcPromotions\promo_ops::calc_total_discount($adj_items);
                if ($discount_amt > $adj_amt)
                {
                    $discount_amt = $adj_amt;
                    $discount_items = $adj_items;
                }
                continue;
            }
        } // foreach

        return $discount_items;
    }

    /**
     * Calculate the best discount for a product given an FEU user id, and a product id.
     *
     * @returns a hash of results for the best discount found or FALSE.
     * @deprecated
     */
    static public function get_discount_for_product($feu_uid, $product_id)
    {
        $sess = new \xt_session('EcPromotions');
        $gCms = cmsms();
        $db = $gCms->GetDb();
        $date = $db->DbTimeStamp(time());

        // get a list of the currently valid promotions.
        $query = 'SELECT id FROM ' . PROMOTIONS_TABLE . "
               WHERE (start_date <= $date) AND (end_date >= $date)
               ORDER BY item_order,offer_type ASC";
        $promotion_ids = $db->GetCol($query);
        if (! $promotion_ids)
        {
            return FALSE;
        }

        // now we have a list of promotions applicable for this date
        // load each one and test it.
        $best_discount = '';
        foreach ($promotion_ids as $promo_id)
        {
            $promo = \EcPromotions\promo_ops::load_promo_by_id($promo_id);
            if (! $promo)
            {
                continue;
            }

            $match = true;
            for ($i = 0; $i < $promo->count_conditions(); $i ++)
            {
                $condition = $promo->get_condition($i);

                switch ($condition->get_cond_type())
                {
                    case PROMOTIONS_COND_FEU:
                        if (! \EcPromotions\promo_tests::test_feu_uid($feu_uid, $promo))
                        {
                            $match = false;
                        }
                        break;

                    case PROMOTIONS_COND_COUPON:
                        $coupons = $sess->get('coupons', '');
                        if ($coupons)
                        {
                            // test all of our coupons.
                            $test = false;
                            foreach ($coupons as $coupon)
                            {
                                if (\EcPromotions\promo_tests::test_coupon($coupon, $promo))
                                {
                                    $test = true;
                                    break;
                                }
                            }
                            if (! $test)
                            {
                                $match = false;
                                break;
                            }
                        }
                        else
                        {
                            $match = false;
                        }
                        break;

                    case PROMOTIONS_COND_PRODID:
                        if (! \EcPromotions\promo_tests::single_product_matches($promo, $product_id))
                        {
                            $match = false;
                        }
                        break;

                    case PROMOTIONS_COND_PRODCAT:
                        if (! \EcPromotions\promo_tests::single_product_from_category($promo, $product_id))
                        {
                            $match = false;
                        }
                        break;

                    case PROMOTIONS_COND_PRODHIER:
                        if (! \EcPromotions\promo_tests::single_product_from_hierarchy($promo, $product_id))
                        {
                            $match = false;
                        }
                        break;
                }
            }

            if ($match)
            {
                // this promotion matches the product id
                $res = \EcPromotions\promo_ops::calculate_product_discount($promo, $product_id);
                if (is_array($res))
                {
                    if (! is_array($best_discount))
                    {
                        $best_discount = $res;
                        $best_discount['promo_id'] = $promo->get_id();
                    }
                    elseif ($res['percentage'] < $best_discount['percentage'])
                    {
                        $best_discount = $res;
                        $best_discount['promo_id'] = $promo->get_id();
                    }
                }
            }
        }

        if (is_array($best_discount))
        {
            return $best_discount;
        }

        return FALSE;
    }

    static public function clear_coupons()
    {
        $sess = new \xt_session('EcPromotions');
        $sess->clear();
    }

    static public function add_coupon($code)
    {
        $sess = new \xt_session('EcPromotions');
        $tmp = $sess->get('coupons', '');
        if (is_array($tmp))
        {
            if (! in_array($code, $tmp))
            {
                $tmp[] = $code;
            }
        }
        else
        {
            $tmp = array();
            $tmp[] = $code;
        }
        $sess->put('coupons', $tmp);
    }

    static public function get_coupons()
    {
        $sess = new \xt_session('EcPromotions');
        $tmp = $sess->get('coupons', '');
        if (is_array($tmp))
        {
            return $tmp;
        }
    }

    /**
     * A smarty plugin for finding the discount for a product
     */
    public static function smarty_function_promo_get_prod_discount($params, &$smarty)
    {
        $feu_uid = - 1;
        $product_id = - 1;
        if (isset($params['feu_uid']))
        {
            $feu_uid = (int) $params['feu_uid'];
        }
        else
        {
            $feu_module = module_helper::get_instance('FrontEndUsers');
            $feu_uid = $feu_module->LoggedInId();
        }

        if (isset($params['product_id']))
        {
            $product_id = (int) $params['product_id'];
        }

        if ($product_id < 1 || $feu_uid < 1)
        {
            return; // invalid/insufficient params
        }

        $res = self::get_discount_for_product($feu_uid, $product_id);
        if ($res)
        {
            $res['decimal'] = $res['percentage'];
            $res['percentage'] *= 100.0;
        }
        if (isset($params['assign']))
        {
            $smarty->assign($params['assign'], $res);
            return;
        }

        return $res;
    }

    public function get_promotion_assistant()
    {
        if (! $this->_helper)
        {
            $this->_helper = new \EcPromotions\promotion_assistant($this);
        }

        return $this->_helper;
    }

} // class

#
# EOF
#
?>
