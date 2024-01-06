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

use EcProductMgr;
use EcOrderMgr;

class promo_tests
{

    static public function &_get_feu()
    {
        return \cms_utils::get_module('MAMS');
    }

    /**
     * Test the promotion to see if the promotion is applicable
     * to coupon code supplied.
     *
     * @param
     *            string coupon code
     * @param
     *            promotion Promotion object.
     * @return TRUE if this test does not rely on coupon codes, or if the coupon code matches. FALSE otherwise.
     */
    static public function test_coupon($coupon, promotion $promo)
    {
        $conditions = $promo->get_conditions();
        $fnd = FALSE;
        for ($i = 0; $i < count($conditions); $i ++)
        {
            $cond = $conditions[$i];
            if ($cond->get_cond_type() != PROMOTIONS_COND_COUPON)
            {
                continue;
            }

            $fnd = TRUE;
            $data = $cond->get_data();
            if (strcasecmp($coupon, $data) == 0)
            {
                return TRUE;
            }
        }

        if (! $fnd)
        {
            return TRUE; // this promition has no coupon dependencies.
        }

        // this promotion does not match the supplied coupon.
        return FALSE;
    }

    /**
     * Test the promotion to see if it is subject to a weight condition
     * and wether the weight supplies matches that condition.
     *
     * @param
     *            float weight amount
     * @param
     *            promotion Promotion object.
     * @return TRUE if this test does not rely on order weight, or if the weight value is over the allowed range.  FALSE otherwise.
     */
    static public function test_order_weight($weight, promotion $promo)
    {
        if ($weight <= 0.00)
        {
            return FALSE;
        }

        $conditions = $promo->get_conditions();
        for ($i = 0; $i < count($conditions); $i ++)
        {
            $cond = $conditions[$i];
            if ($cond->get_cond_type() != PROMOTIONS_COND_WEIGHT)
            {
                continue;
            }

            $data = $cond->get_data();
            if ($weight >= $data)
            {
                return TRUE;
            }

            break;
        }

        // this promotion is not dependant upon order weights.
        return FALSE;
    }

    /**
     * Test the promotion to see if the promotion is applicable
     * to order subtotal calculations, and if so if the supplied
     * subtotal is over or equal to the one specified in the
     * conditions.
     *
     * @param
     *            float subtotal amount
     * @param
     *            promotion Promotion object.
     * @return    TRUE if this test does not rely on orderr subtotal, or if the subtotal
     *            value is over the allowed range.  FALSE otherwise.
     */
    static public function test_order_subtotal($subtotal, &$promo)
    {
        if ($subtotal <= 0.00)
        {
            return FALSE;
        }

        $conditions = $promo->get_conditions();
        $found = false;
        for ($i = 0; $i < count($conditions); $i ++)
        {
            $cond = &$conditions[$i];
            if ($cond->get_cond_type() != PROMOTIONS_COND_SUBTOTAL)
            {
                continue;
            }

            $data = $cond->get_data();
            if ($subtotal >= $data)
            {
                return TRUE;
            }

            break;
        }

        // this promotion is not dependant upon order subtotals.
        return FALSE;
    }

    /**
     * Test the promotion to see if the promotion is
     * applicable to certain FEU groups...
     * and if so
     * if the supplied feu_group passes the test.
     *
     * @param
     *            int feu_uid
     * @param
     *            promotion Promotion object.
     * @return    TRUE if this test does not rely on FEU group membership, or if the user
     *            is member of one of the specified group names.  FALSE otherwise.
     */
    static public function test_feu_uid($feu_uid, &$promo)
    {
        if ($feu_uid <= 0)
        {
            return FALSE;
        }

        $feu = self::_get_feu();
        if (! $feu)
        {
            return FALSE;
        }

        $conditions = $promo->get_conditions();
        for ($i = 0; $i < count($conditions); $i ++)
        {
            $cond = $conditions[$i];
            if ($cond->get_cond_type() == PROMOTIONS_COND_FEU)
            {
                // get member groups as array of group names
                $member_of = $feu->GetMemberGroupsArray($feu_uid);
                if (! $member_of)
                {
                    return FALSE;
                }
                $member_of = \xt_array::extract_field($member_of, 'groupid');

                $tmp = $feu->GetGroupList();
                $allgroups = array_flip($tmp);
                $member_group_names = array();
                foreach ($member_of as $onegid)
                {
                    $member_group_names[] = $allgroups[$onegid];
                }

                // get the value from the condition
                // as an array of trimmed group names.
                $data = explode(',', $cond->get_data());
                for ($j = 0; $j < count($data); $j ++)
                {
                    $data[$j] = trim($data[$j]);
                }

                // now do an intersection between member groupo names
                // and the data group names.
                $res = array_intersect($member_group_names, $data);
                if (count($res))
                {
                    return TRUE;
                }

                // there is guaranteed to be no more conditions of this type.
                break;
            }
        }

        // this promotion is not dependant upon feu group membership.
        return FALSE;
    }

    /**
     * Test the promotion to see if the product id specified
     * is applicable to product hierarchy tests, and if so, if the product id specified
     * is in the appropriate product hierarchy.
     *
     * @param
     *            promotion object
     * @param
     *            product id
     * @return    TRUE if the product is applicable to this promotion, FALSE otherwise
     */
    static public function single_product_from_hierarchy(&$promo, $product_id)
    {
        $products = null;
        for ($i = 0; $i < $promo->count_conditions(); $i ++)
        {
            $condition = $promo->get_condition($i);
            switch ($condition->get_cond_type())
            {
                case PROMOTIONS_COND_PRODHIER:
                    $products = EcProductMgr::GetProductIdsFromHierarchy($condition->get_data(), '.');
                    $i = $promo->count_conditions();
                    break;

                default:
                    continue;
                    break;
            }
        }

        // check if there are products found
        if (! $products)
        {
            return FALSE;
        }

        // now check to see if the items in the cart match.
        if (in_array($product_id, $products))
        {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Test the promotion to see if it is applicable to product hierarchy
     * conditions, and if so, if the items in the users cart are products,
     * and if they match the conditions in the promotion.
     *
     * @param
     *            array of line_item objects
     * @param
     *            promotion Promotion object.
     * @return    TRUE if this test does not rely on FEU group membership, or if
     *            the user is member of one of the specified group names.  FALSE otherwise.
     */
    static public function test_product_from_hierarchy($items, &$promo)
    {
        $products = '';
        for ($i = 0; $i < $promo->count_conditions(); $i ++)
        {
            $condition = $promo->get_condition($i);
            switch ($condition->get_cond_type())
            {
                case PROMOTIONS_COND_PRODHIER:
                    $products = EcProductMgr::GetProductIdsFromHierarchy($condition->get_data(), '.');
                    $i = $promo->count_conditions();
                    break;

                default:
                    continue;
                    break;
            }
        }

        // check if there are products found
        if (! $products)
        {
            return FALSE;
        }

        // now check to see if the items in the cart match.
        for ($i = 0; $i < count($items); $i ++)
        {
            $item = $items[$i];
            if ($item->get_item_type() == EcOrderMgr\LineItem::ITEMTYPE_PRODUCT && in_array($item->get_item_id(), $products))
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Test the promotion to see if it is applicable to product category
     * conditions, and if so, does the product id supplied match
     *
     * @param
     *            the promotion object
     * @param
     *            the product id
     * @return    TRUE if match found, FALSE otherwise.
     */
    static public function single_product_from_category(&$promo, $product_id)
    {
        $products = '';
        for ($i = 0; $i < $promo->count_conditions(); $i ++)
        {
            $condition = $promo->get_condition($i);
            switch ($condition->get_cond_type())
            {
                case PROMOTIONS_COND_PRODCAT:
                    $products = EcProductMgr::GetProductIdsFromCategories($condition->get_data());
                    $i = $promo->count_conditions();
                    break;

                default:
                    continue;
                    break;
            }
        }

        // check if there are products found
        if (! $products)
        {
            return FALSE;
        }

        // now check to see if the product id matches.
        $item = $items[$i];
        if (in_array($product_id, $products))
        {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Test the promotion to see if it is applicable to product category
     * conditions, and if so do the product items in the current
     * users cart match the conditions in the promotion.
     *
     * @param
     *            array of line_item objects
     * @param
     *            promotion Promotion object.
     * @return    TRUE if this test does not rely on FEU group membership,
     *            or if the user is member of one of the specified group names.
     *            FALSE otherwise.
     */
    static public function test_product_from_category($items, &$promo)
    {
        $products = '';
        for ($i = 0; $i < $promo->count_conditions(); $i ++)
        {
            $condition = $promo->get_condition($i);
            switch ($condition->get_cond_type())
            {
                case PROMOTIONS_COND_PRODCAT:
                    $products = EcProductMgr::GetProductIdsFromCategories($condition->get_data());
                    $i = $promo->count_conditions();
                    break;

                default:
                    continue;
                    break;
            }
        }

        // check if there are products found
        if (! $products)
        {
            return FALSE;
        }

        // now check to see if the items in the cart match.
        for ($i = 0; $i < count($cart_items); $i ++)
        {
            $item = $items[$i];
            if ($item->get_item_type == EcOrderMgr\LineItem::ITEMTYPE_PRODUCT
                && in_array($item->get_item_id(), $products))
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Test the promotion to see if it is applicable to conditions based on a single product
     * and if so, see if the product id specifies matches.
     *
     * @param
     *            the promotion object
     * @param
     *            the product id to test
     * @return    TRUE if match found, false otherwise
     */
    static public function single_product_matches(&$promo, $product_id)
    {
        $product = null;
        for ($i = 0; $i < $promo->count_conditions(); $i ++)
        {
            $condition = $promo->get_condition($i);
            switch ($condition->get_cond_type())
            {
                case PROMOTIONS_COND_PRODID:
                    $product = $condition->get_data();
                    $i = $promo->count_conditions();
                    break;

                default:
                    continue;
                    break;
            }
        }

        // check if there are products found
        if (! $product)
        {
            return FALSE;
        }

        $p_array = explode(',', $product);
        for ($i = 0; $i < count($p_array); $i ++)
        {
            $p_array[$i] = (int) trim($p_array[$i]);
        }

        // now check to see if the items in the cart match.
        if (in_array($product_id, $p_array))
        {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Test the promotion to see if it is applicable to conditions based on a single product
     * and if so, see if at least one product in the line items supplied match the condition.
     *
     * @param
     *            array of line_item objects
     * @param
     *            promotion Promotion object.
     * @return    TRUE if this test does not rely on FEU group membership, or if the user is
     *            member of one of the specified group names.  FALSE otherwise.
     */
    static public function test_single_product($items, &$promo)
    {
        $product = null;
        for ($i = 0; $i < $promo->count_conditions(); $i ++)
        {
            $condition = $promo->get_condition($i);
            switch ($condition->get_cond_type())
            {
                case PROMOTIONS_COND_PRODID:
                    $product = $condition->get_data();
                    $i = $promo->count_conditions();
                    break;

                default:
                    continue;
                    break;
            }
        }

        // check if there are products found
        if (! $product)
        {
            return FALSE;
        }

        $p_array = explode(',', $product);
        for ($i = 0; $i < count($p_array); $i ++)
        {
            $p_array[$i] = (int) trim($p_array[$i]);
        }

        // now check to see if the items in the cart match.
        for ($i = 0; $i < count($items); $i ++)
        {
            $item = $items[$i];
            if ($item->get_item_type() == EcOrderMgr\LineItem::ITEMTYPE_PRODUCT
                && in_array($item->get_item_id(), $p_array))
            {
                return TRUE;
            }
        }

        return FALSE;
    }

} // end of class

#
# EOF
#
?>
