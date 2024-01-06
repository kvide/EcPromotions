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

use EcommerceExt\ProductMgr;
use EcommerceExt\ecomm;

/**
 * This class is an assistant to the promotion_tester class.
 * This class operates on single promotions.
 */
class promo_tester
{

    private $_p;
    private $_sku;
    private $_product;
    private $_quantity;
    private $_cart;
    private $_auto_feu = TRUE;
    private $_feu_uid = null;
    private $_coupons = null;
    private $_ignore_discounted;

    private function _get_sku()
    {
        $sku = null;
        if ($this->_product)
        {
            $sku = $this->_product->get_sku();
        }
        if ($this->_sku)
        {
            $sku = $this->_sku;
        }

        return $sku;
    }

    public function __construct(promotion $promo)
    {
        $this->_p = $promo;
    }

    public function set_auto_feu($flag = TRUE)
    {
        $this->_auto_feu = (bool) $flag;
    }

    public function set_feu_uid($uid)
    {
        if ($uid > 0)
        {
            $this->_feu_uid = $uid;
        }
    }

    private function _get_feu_uid()
    {
        if (! is_null($_feu_uid))
        {
            return $this->_feu_uid;
        }

        if ($this->_auto_feu)
        {
            $feu = \cms_utils::get_module('MAMS');
            if (is_object($feu))
            {
                $this->_feu_uid = $feu->LoggedInId();
                return $this->_feu_uid;
            }
        }
    }

    private function _item_discounted($idx)
    {
        if ($this->_ignore_discounted)
        {
            return FALSE;
        }

        for ($i = 0; $i < count($this->_cart); $i ++)
        {
            $item = $this->_cart[$i];
            if ($item->get_parent() != $idx)
            {
                continue;
            }
            if ($item->get_parent() > - 1)
            {
                return TRUE;
            }
            if ($item->get_unit_discount() != 0)
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function set_sku($sku)
    {
        $this->_sku = $sku;
    }

    public function set_product(ProductMgr\productinfo $product)
    {
        $this->_product = $product;
    }

    public function set_quantity($nn)
    {
        $this->_quantity = (int) $nn;
    }

    public function set_ignore_discounted($val)
    {
        $this->_ignore_discounted = cms_to_bool($val);
    }

    public function set_cart($items)
    {
        if (is_array($items) && count($items))
        {
            $this->_cart = $items;
        }
    }

    public function set_coupons($items)
    {
        if (is_array($items) && count($items))
        {
            $this->_coupons = $items;
        }
    }

    /**
     * Calculates the 'subtotal' of all cart items
     * including disscounts already applied
     */
    private function _calc_order_subtotal($include_discounted = FALSE)
    {
        $v = 0;
        if (is_array($this->_cart) && count($this->_cart))
        {
            foreach ($this->_cart as $item)
            {
                if (! is_a($item, '\Ecommerce\cartitem'))
                {
                    // should throw an exception here
                    continue;
                }

                if (! $include_discounted && $item->get_unit_discount() != 0)
                {
                    continue;
                }
                if (! $include_discounted && $item->get_parent() >= 0)
                {
                    continue;
                }
                $v += $item->get_item_total();
            }
        }

        return $v;
    }

    private function _calc_order_weight()
    {
        $v = 0;
        if (is_array($this->_cart) && count($this->_cart))
        {
            foreach ($this->_cart as $item)
            {
                if (! is_a($item, '\Ecommerce\cartitem'))
                {
                    // should throw an exception here
                    continue;
                }

                $v += $item->get_unit_weight() * $item->get_quantity();
            }
        }

        return $v;
    }

    private function _get_sku_quantity($sku)
    {
        $v = 0;
        if (! $sku)
        {
            return $v;
        }

        if (is_array($this->_cart) && count($this->_cart))
        {
            foreach ($this->_cart as $item)
            {
                if (! is_a($item, '\Ecommerce\cartitem'))
                {
                    // should throw an exception here
                    continue;
                }

                if ($sku != $item->get_sku())
                {
                    continue;
                }
                $v += $item->get_quantity();
            }
        }

        return $v;
    }

    // returns boolean
    public function find_match()
    {
        $match_count = 0;
        for ($i = 0; $i < $this->_p->count_conditions(); $i ++)
        {
            $condition = $this->_p->get_condition($i);

            switch ($condition->get_cond_type())
            {
                case PROMOTIONS_COND_FEU:
                    // test user is member of feu group.
                    if (condition_tests::test_feu_uid($condition))
                    {
                        $match_count ++;
                    }
                    break;

                case PROMOTIONS_COND_SUBTOTAL:
                    if (! is_numeric($condition->get_data()))
                    {
                        break;
                    }
                    if ($this->_calc_order_subtotal() >= (float) $condition->get_data())
                    {
                        $match_count ++;
                    }
                    break;

                case PROMOTIONS_COND_WEIGHT:
                    if (! is_numeric($condition->get_data()))
                    {
                        break;
                    }
                    if ($this->_calc_order_weight() >= (float) $condition->get_data())
                    {
                        $match_count ++;
                    }
                    break;

                case PROMOTIONS_COND_COUPON:
                    if (is_array($this->_coupons) && count($this->_coupons))
                    {
                        foreach ($this->_coupons as $code)
                        {
                            if (strcasecmp($code, $condition->get_data()) == 0)
                            {
                                $match_count ++;
                                break;
                            }
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODID:
                    if ($this->_product)
                    {
                        // test if this product matches the specified product id
                        $tmp = explode(',', $condition->get_data());
                        if (in_array($this->_product->get_product_id(), $tmp))
                        {
                            $match_count ++;
                            break;
                        }
                    }
                    break;

                case PROMOTIONS_COND_BULKPURCHASE:
                    if (($sku = $this->_get_sku()))
                    {
                        // test if this sku matches
                        $data = $condition->get_data();
                        if (fnmatch($sku, $data[0]))
                        {
                            // now test if the quantity matches
                            $num = $this->_get_sku_quantity($sku);
                            $num += (int) $this->_quantity;
                            if ($num > 0 && $num >= $data[1])
                            {
                                $match_count ++;
                                break;
                            }
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODSKU:
                    if (($sku = $this->_get_sku()))
                    {
                        // test if this sku matches the specified product sku's
                        $tmp = explode(',', $condition->get_data());
                        foreach ($tmp as $one)
                        {
                            if (fnmatch($one, $sku))
                            {
                                $match_count ++;
                                break;
                            }
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODCAT:
                    $products = ProductMgr::GetProductIdsFromCategories($condition->get_data());
                    if ($products)
                    {
                        // have product ids in thse categories.
                        if ($this->_product && in_array($this->_product->get_product_id(), $products))
                        {
                            $match_count ++;
                            break;
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODHIER:
                    $products = ProductMgr::GetProductIdsFromHierarchy($condition->get_data(), '.');
                    if ($products)
                    {
                        // have product ids in thse categories.
                        if ($this->_product && in_array($this->_product->get_product_id(), $products))
                        {
                            $match_count ++;
                            break;
                        }
                    }
                    break;
            } // switch
        } // for each condition

        if ($match_count == $this->_p->count_conditions())
        {
            if ($this->_p->get_extra('allow_once'))
            {
                foreach ($this->_cart as $one_item)
                {
                    if ($one_item->get_promo() == $this->_p->get_id())
                    {
                        return FALSE;
                    }
                }
            }

            // it matches.
            return TRUE;
        }

        return FALSE;
    }

    // end of function
    public function find_offer_match()
    {
        // tests if the product, or sku match the offer
        // note, only works with offer types that have a product id, or sku in them.
        // returns boolean.
        switch ($this->_p->get_offer_type())
        {
            case PROMOTIONS_OFFER_PRODUCT:
                if ($this->_product->get_product_id() == $this->_p->get_offer_data())
                {
                    return TRUE;
                }
                break;

            case PROMOTIONS_OFFER_PRODUCTSKU:
                if (($sku = $this->_get_sku()))
                {
                    $tmp = explode(',', $this->_p->get_offer_data());
                    foreach ($tmp as $one)
                    {
                        if (fnmatch($one, $sku))
                        {
                            return TRUE;
                        }
                    }
                }
                break;
        }

        return FALSE;
    }

    // end of function

    /**
     * Test if the contents of the cart match all of the conditions for this promotion.
     * At least one of the conditions has to be a 'product' type match.
     */
    public function find_cart_match(&$matched_cartitem_idx)
    {
        $match_count = 0;
        $match_cartitem = - 1;
        for ($i = 0; $i < $this->_p->count_conditions(); $i ++)
        {
            $condition = $this->_p->get_condition($i);

            switch ($condition->get_cond_type())
            {
                case PROMOTIONS_COND_FEU:
                    // test user is member of feu group.
                    if (condition_tests::test_feu_uid($condition))
                    {
                        $match_count ++;
                    }
                    break;

                case PROMOTIONS_COND_SUBTOTAL:
                    $data = $condition->get_data();
                    if (! is_numeric($data))
                    {
                        break;
                    }
                    if ($this->_calc_order_subtotal() >= (float) $data)
                    {
                        $match_count ++;
                    }
                    break;

                case PROMOTIONS_COND_WEIGHT:
                    if (! is_numeric($condition->get_data()))
                    {
                        break;
                    }
                    if ($this->_calc_order_weight() >= (float) $condition->get_data())
                    {
                        $match_count ++;
                    }
                    break;

                case PROMOTIONS_COND_COUPON:
                    if (is_array($this->_coupons) && count($this->_coupons))
                    {
                        foreach ($this->_coupons as $code)
                        {
                            if (strcasecmp($code, $condition->get_data()) == 0)
                            {
                                $match_count ++;
                                break;
                            }
                        }
                    }
                    break;

                case PROMOTIONS_COND_BULKPURCHASE:
                    if ($this->_cart)
                    {
                        $idx = 0;
                        $data = $condition->get_data();
                        foreach ($this->_cart as $item)
                        {
                            if (! $this->_item_discounted($idx))
                            {
                                if (fnmatch($data[0], $item->get_sku()))
                                {
                                    if ($data[1] > 0 && $item->get_quantity() >= (int) $data[1])
                                    {
                                        $match_count ++;
                                        $match_cartitem = $idx;
                                        break;
                                    }
                                }
                            }
                            $idx ++;
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODID:
                    // test if this product exists in the cart (by product id)
                    if ($this->_cart)
                    {
                        // if matches, then there is a product in the cart that matches this condition
                        $idx = 0;
                        foreach ($this->_cart as $item)
                        {
                            if (! $this->_item_discounted($idx))
                            {
                                $tmp = explode(',', $condition->get_data());
                                if (in_array($item->get_product_id(), $tmp))
                                {
                                    $match_count ++;
                                    $match_cartitem = $idx;
                                    break;
                                }
                            }
                            $idx ++;
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODSKU:
                    // test if this product exists in the cart (by sku)
                    if ($this->_cart)
                    {
                        // if matches, then there is a product in the cart that matches this condition
                        $idx = 0;
                        foreach ($this->_cart as $item)
                        {
                            if ($this->_item_discounted($idx))
                            {
                                continue;
                            }

                            $tmp = explode(',', $condition->get_data());
                            $fnd = false;
                            foreach ($tmp as $one)
                            {
                                if (fnmatch($one, $item->get_sku()))
                                {

                                    $match_count ++;
                                    $match_cartitem = $idx;
                                    $fnd = true;
                                    break;
                                }
                            }
                            if ($fnd)
                            {
                                break;
                            }
                            $idx ++;
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODCAT:
                    $products = ProductMgr::GetProductIdsFromCategories($condition->get_data());
                    if ($products)
                    {
                        if ($this->_cart && ! $this->_test_productonly)
                        {
                            $idx = 0;
                            foreach ($this->_cart as $item)
                            {
                                if ($item->get_parent() > - 1)
                                {
                                    continue;
                                }
                                if ($item->get_unit_discount() != 0)
                                {
                                    continue;
                                }

                                if (in_array($item->get_product_id(), $products))
                                {
                                    $match_count ++;
                                    $match_cartitem = $idx;
                                    break;
                                }
                                $idx ++;
                            }
                        }
                    }
                    break;

                case PROMOTIONS_COND_PRODHIER:
                    $products = ProductMgr::GetProductIdsFromHierarchy($condition->get_data(), '.');
                    if ($products)
                    {
                        if ($this->_cart && ! $this->_test_productonly)
                        {
                            $idx = 0;
                            foreach ($this->_cart as $item)
                            {
                                if (in_array($item->get_product_id(), $products))
                                {
                                    $match_count ++;
                                    $match_cartitem = $idx;
                                    break;
                                }
                                $idx ++;
                            }
                        }
                    }
                    break;
            } // switch
        }

        if (($match_count == $this->_p->count_conditions()))
        {
            // it matches.
            $matched_cartitem_idx = $match_cartitem;
            return TRUE;
        }

        return FALSE;
    }

} // end of class

#
# EOF
#
?>
