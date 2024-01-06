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

use EcOrderMgr;
use EcProductMgr;
use EcommerceExt\ecomm;

class promo_ops
{

    private function __construct()
    {
    }

    private static function &_get_db()
    {
        $gCms = cmsms();
        $db = &$gCms->GetDb();

        return $db;
    }

    private static function &_get_products_mod()
    {
        return \cms_utils::get_module('EcProductMgr');
    }

    private static function &_get_product($product_id)
    {
        $prod = ecomm::get_product_info('Products', $product_id);

        return $prod;
    }

    public static function load_promo_by_id($id)
    {
        return promotion::load_by_id($id);
    }

    public static function is_valid_coupon($code)
    {
        $db = self::_get_db();
        $query = 'SELECT A.id FROM ' . PROMOTIONS_TABLE . ' A
                LEFT JOIN ' . PROMOTIONS_COND_TABLE . ' B
                  ON A.id = B.promotion_id
               WHERE NOW() BETWEEN A.start_date AND A.end_date
                 AND B.cond_type = ?
                 AND B.data = ?';
        $tmp = $db->GetOne($query, array(PROMOTIONS_COND_COUPON, $code));
        return $tmp;
    }

    static public function check_unused_coupon($name, $allow_promo = '')
    {
        // hmmm, maybe we should allow reuse of coupon codes that are expired.
        $db = self::_get_db();
        $where = $parms = [];

        $where[] = 'cond_type = ?';
        $parms[] = PROMOTIONS_COND_COUPON;
        $where[] = 'data = ?';
        $parms[] = $name;
        if ($allow_promo)
        {
            $where[] = 'promotion_id != ?';
            $parms[] = $allow_promo;
        }
        $query = 'SELECT id FROM ' . PROMOTIONS_COND_TABLE;
        $query .= ' WHERE ' . implode(' AND ', $where);
        $tmp = $db->GetOne($query, $parms);
        if ($tmp)
        {
            return FALSE;
        }

        return TRUE;
    }

    static public function check_unused_name($name, $id = '')
    {
        $gCms = cmsms();
        $db = $gCms->GetDb();

        if (empty($id))
        {
            $query = 'SELECT id FROM ' . PROMOTIONS_TABLE . ' WHERE name = ?';
            $tmp = $db->GetOne($query, array(
                $name
            ));
            if ($tmp)
            {
                return FALSE;
            }
            return TRUE;
        }

        $query = 'SELECT id FROM ' . PROMOTIONS_TABLE . ' WHERE name = ? AND id != ?';
        $tmp = $db->GetOne($query, array($name, $id));
        if ($tmp)
        {
            return FALSE;
        }

        return TRUE;
    }

    public static function calc_total_discount($cart_items)
    {
        $discount = 0;
        for ($i = 0; $i < count($cart_items); $i ++)
        {
            $discount += $cart_items[$i]->get_discount();
        }

        return $discount;
    }

    /**
     * Given a product id and an applicable (already tested) promotion
     * calculate the discount (if any) for this product
     *
     * @param
     *            the promotion object
     * @param
     *            the product id
     * @returns FALSE on failure, or a hash containing the different
     *  discount attributes.
     */
    public static function calculate_product_discount(&$promo, $product_id)
    {
        $product = self::_get_product($product_id);
        if (! is_array($product))
        {
            return FALSE;
        }

        $percent = '';
        $discount = '';
        $free_product = '';
        switch ($promo->get_offer_type())
        {
            case PROMOTIONS_OFFER_PRODUCT:
                // we get a free product.
                $free_product = $promo->get_offer_data();
                break;

            case PROMOTIONS_OFFER_PERCENT:
                // calculate N % off
                $percent = $promo->get_offer_data() * - 1.0;
                break;

            case PROMOTIONS_OFFER_DISCOUNT:
                // calculate $N off
                $discount = $promo->get_offer_data() * - 1.0;
                break;

            case PROMOTIONS_OFFER_PRODDISCOUNT:
                $percent = $promo->get_offer_data() * - 1.0;
                break;
        }

        if (empty($percent))
        {
            $percent = $discount / $product->get_price();
        }
        else if (empty($discount))
        {
            $discount = $product->get_price() * $percent;
        }

        $res = array('product_id' => $free_product);
        $res['percentage'] = $percent;
        $res['discount'] = $discount;

        return $res;
    }

    public static function calculate_discount(&$promo, $subtotal, $cart_items)
    {
        $new_items = array();
        switch ($promo->get_offer_type())
        {
            case PROMOTIONS_OFFER_PERCENT:
                // calculate N % off each item... an fill in the discount field.
                for ($i = 0; $i < count($cart_items); $i ++)
                {
                    $item = $cart_items[$i];
                    $item->set_discount($item->get_unit_price() * $promo->get_offer_data() * - 1.0);
                    $new_items[] = $item;
                }
                break;

            case PROMOTIONS_OFFER_DISCOUNT:
                // calculate $N off
                for ($i = 0; $i < count($cart_items); $i ++)
                {
                    $item = clone ($cart_items[$i]);
                    $new_items[] = $item;
                }
                // $item = new line_item();
                $item = new EcOrderMgr\LineItem();
                $item->set_source('Products');
                $item->set_description($promo->get_name());
                // $item->set_item_type(line_item::ITEMTYPE_DISCOUNT);
                $item->set_item_type(EcOrderMgr\LineItem::ITEMTYPE_DISCOUNT);
                $item->set_quantity(1);
                $item->set_discount($promo->get_offer_data() * - 1.0);
                $new_items[] = $item;
                break;

            case PROMOTIONS_OFFER_PRODUCT:
                // offer a free product.
                for ($i = 0; $i < count($cart_items); $i ++)
                {
                    $item = clone ($cart_items[$i]);
                    $new_items[] = $item;
                }
                // $item = new line_item();
                $item = new EcOrderMgr\LineItem();
                $item->set_source('Products');
                $item->set_description($promo->get_name());
                // $item->set_item_type(line_item::ITEMTYPE_PRODUCT);
                $item->set_item_type(EcOrderMgr\LineItem::ITEMTYPE_PRODUCT);
                $item->set_status(\EcommerceExt\ITEMSTATUS_NOTSHIPPED);
                $item->set_quantity(1);
                $product = self::_get_product($promo->get_offer_data());
                if (! $product)
                {
                    return FALSE;
                }
                $item->set_unit_price($product->get_price());
                $item->set_discount($product->get_price() * - 1.0);
                $item->set_weight($product->get_weight());
                $item->set_sku($product->get_sku());
                $new_items[] = $item;
                break;

            case PROMOTIONS_OFFER_PRODDISCOUNT:
                // discount the applicable products by a percentage
                $products = array();
                for ($i = 0; $i < $promo->count_conditions(); $i ++)
                {
                    $condition = $promo->get_condition($i);
                    switch ($condition->get_cond_type())
                    {
                        case PROMOTIONS_COND_PRODID:
                            $products = explode(',', $condition->get_data());
                            $i = $promo->count_conditions();
                            break;

                        case PROMOTIONS_COND_PRODHIER:
                            // $products = Products::GetProductIdsFromHierarchy($condition->get_data(),'.');
                            $products = EcProductMgr::GetProductIdsFromHierarchy($condition->get_data(), '.');
                            $i = $promo->count_conditions();
                            break;

                        case PROMOTIONS_COND_PRODCAT:
                            // $products = Products::GetProductIdsFromCategories($condition->get_data());
                            $products = EcProductMgr::GetProductIdsFromCategories($condition->get_data());
                            $i = $promo->count_conditions();
                            break;

                        default:
                            continue;
                            break;
                    }
                }

                // check if we found matching products?
                if (! $products)
                {
                    break;
                }

                // we did... so we have to do something with each product..
                for ($i = 0; $i < count($cart_items); $i ++)
                {
                    $item = clone ($cart_items[$i]);
                    if ($item->get_item_type() == EcOrderMgr\LineItem::ITEMTYPE_PRODUCT && in_array($item->get_item_id(), $products))
                    {
                        $item->set_discount($promo->get_offer_data() * $item->get_unit_price() * - 1);
                    }
                    $new_items[] = $item;
                }
                break;
        }

        return $new_items;
    }

} // class

#
# EOF
#
?>
