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

use EcommerceExt\Promotion;

// this class tests all promotions of the appropriate type against the item, and the cart.
class promotion_tester extends \EcommerceExt\Promotion\promotion_tester
{

    private $_coupons;
    private $_feu_uid;

    public function __construct()
    {
        $sess = new \xt_session('EcPromotions');
        $this->_coupons = $sess->get('coupons', '');

        parent::__construct();
    }

    public function get_feu_uid()
    {
        $x = parent::get_feu_uid();
        if ($x > 0)
        {
            return $x;
        }

        $feu = \cms_utils::get_module('MAMS');
        if ($feu)
        {
            $x = $feu->LoggedInId();
            if ($x > 0)
                $this->set_feu_uid($x);
        }

        return $x;
    }

    /**
     * tests if product matches any promotions that match set criteria.
     * (product must be set). Stops after the first match
     *
     * @return \EcommerceExt\Promotion\promotion_match
     */
    public function find_match()
    {
        $ecommMod = \cms_utils::get_module(\MOD_ECOMMERCEEXT);
        // converts a ecomm type to a promotion type
        $type = \EcPromotions\promotion::TYPE_CHECKOUT;
        switch ($this->get_promo_type())
        {
            case self::TYPE_INSTANT:
                $type = \EcPromotions\promotion::TYPE_INSTANT;
                break;
            case self::TYPE_CHECKOUT:
                $type = \EcPromotions\promotion::TYPE_CHECKOUT;
                break;
            default:
                throw new \CmsInvalidDataException('Unknown promotion type in ' . __CLASS__);
        }

        $match = null;
        $promos = \EcPromotions\promotion::load_all_by_type($type);
        foreach ($promos as $promo)
        {
            $tester = new promo_tester($promo);
            if ($this->get_sku())
            {
                $tester->set_sku($this->get_sku());
            }
            $tester->set_ignore_discounted($this->get_ignore_discounted());
            $tester->set_product($this->get_product());
            $tester->set_quantity($this->get_quantity());
            $tester->set_cart($this->get_cart());
            $tester->set_coupons($this->_coupons);
            if ($tester->find_match())
            {
                // this promotion matches.
                // need to parse the offer.
                $match = new Promotion\promotion_match();
                if ($type == promotion::TYPE_INSTANT)
                {
                    switch ($promo->get_offer_type())
                    {
                        case PROMOTIONS_OFFER_PRODDISCOUNT:
                            $match->set_type($match::OFFER_PERCENT);
                            $match->set_val($promo->get_offer_data());
                            $match->set_promo($promo->get_id());
                            $amt = $this->get_price() * $promo->get_offer_data() * - 1;
                            $match->set_discount_amt(round($amt, 2));
                            break;

                        case PROMOTIONS_OFFER_PRODAMOUNT:
                            $match->set_type($match::OFFER_DISCOUNT);
                            $match->set_discount_amt(round($promo->get_offer_data(), 2));
                            $match->set_val($promo->get_offer_data());
                            $match->set_promo($promo->get_id());
                            break;

                        case PROMOTIONS_OFFER_SAMEPRODUCT:
                            $match->set_type($match::OFFER_PERCENT);
                            $match->set_val($promo->get_offer_data());
                            $match->set_promo($promo->get_id());
                            break;

                        case PROMOTIONS_OFFER_PRODUCT:
                            // a free product
                            $match->set_type($match::OFFER_PRODUCTID);
                            $match->set_val($promo->get_offer_data());
                            $match->set_promo($promo->get_id());
                            break;

                        case PROMOTIONS_OFFER_PRODUCTSKU:
                            // a free product
                            $match->set_type($match::OFFER_PRODUCTSKU);
                            $match->set_val($promo->get_offer_data());
                            $match->set_promo($promo->get_id());
                            break;

                        default:
                            throw new \CmsInvalidDataException('offer of type ' . $promo->get_offer_type()
                                                                . ' is invalid for promotion of type ' . $type);
                    }
                }
                else
                {
                    switch ($promotion->get_offer_type())
                    {
                        case PROMOTIONS_OFFER_PRODUCT:
                        case PROMOTIONS_OFFER_PRODUCTDISCOUNT:
                        case PROMOTIONS_OFFER_PERCENT:
                        case PROMOTIONS_OFFER_DISCOUNT:
                        case PROMOTIONS_OFFER_SAMEPRODUCT:
                        case PROMOTIONS_OFFER_PRODUCTSKU:
                            die('incomplete -- ' . __FILE__ . ' -- ' . __LINE__);
                            break;

                        default:
                            throw new \CmsInvalidDataException('offer of type ' . $promotion->get_offer_type()
                                                                . ' is invalid for promotion of type ' . $type);
                    }
                }

                return $match;
            }
        }
    }

    // find match

    /**
     * Find the first promotion that matches the items in the cart
     *
     * @return \EcommerceExt\Promotion\promotion_match
     */
    public function find_cart_match()
    {
        // converts a ecomm type to a promotion type
        $type = promotion::TYPE_CHECKOUT;
        switch ($this->get_promo_type())
        {
            case self::TYPE_INSTANT:
                $type = promotion::TYPE_INSTANT;
                break;
            case self::TYPE_CHECKOUT:
                $type = promotion::TYPE_CHECKOUT;
                break;
            default:
                throw new \CmsInvalidDataException('Unknown promotion type in ' . __CLASS__);
        }

        $promos = promotion::load_all_by_type($type);
        $match = null;
        foreach ($promos as $promo)
        {
            // create a promo tester for eaach promotion, and see if we get a match.
            $tester = new promo_tester($promo);
            $tester->set_cart($this->get_cart());
            $tester->set_coupons($this->_coupons);
            $tester->set_ignore_discounted($this->get_ignore_discounted());

            $match_idx = - 1;
            if ($tester->find_cart_match($match_idx))
            {
                // so far, so good... items in the cart match this promo.
                $match = new Promotion\promotion_match();
                switch ($promo->get_offer_type())
                {
                    case PROMOTIONS_OFFER_PRODUCT:
                        $match->set_type($match::OFFER_PRODUCTID);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        break;

                    case PROMOTIONS_OFFER_PRODUCTSKU:
                        $match->set_type($match::OFFER_PRODUCTSKU);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        break;

                    case PROMOTIONS_OFFER_PERCENT:
                        $match->set_type($match::OFFER_PERCENT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        break;

                    case PROMOTIONS_OFFER_DISCOUNT:
                        $match->set_type($match::OFFER_DISCOUNT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        break;

                    case PROMOTIONS_OFFER_PRODDISCOUNT:
                        $match->set_type($match::OFFER_PERCENT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        $amt = $this->get_price() * $promo->get_offer_data() * - 1;
                        $match->set_discount_amt(round($amt, 2));
                        break;

                    case PROMOTIONS_OFFER_PRODAMOUNT:
                        $match->set_type($match::OFFER_DISCOUNT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_discount_amt(round($promo->get_offer_data(), 2));
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        break;

                    default:
                        // an invalid offer type is just ignored.
                        break;
                }

                return $match;
            }
        }
    }

    public function find_all_cart_matches()
    {
        // converts a ecomm type to a promotion type
        $type = \EcPromotions\promotion::TYPE_CHECKOUT;
        switch ($this->get_promo_type())
        {
            case self::TYPE_INSTANT:
                $type = \EcPromotions\promotion::TYPE_INSTANT;
                break;
            case self::TYPE_CHECKOUT:
                $type = \EcPromotions\promotion::TYPE_CHECKOUT;
                break;
            default:
                throw new \CmsInvalidDataException('Unknown promotion type in ' . __CLASS__);
        }

        $promos = \EcPromotions\promotion::load_all_by_type($type);
        $match = null;
        $matches = array();
        foreach ($promos as $promo)
        {
            $tester = new promo_tester($promo);
            $tester->set_cart($this->get_cart());
            $tester->set_coupons($this->_coupons);
            $tester->set_ignore_discounted($this->get_ignore_discounted());

            $match_idx = - 1;
            if ($tester->find_cart_match($match_idx))
            {
                // so far, so good... offer matches this item... and maybe even the cart matches the conditions.
                $match = new Promotion\promotion_match();
                switch ($promo->get_offer_type())
                {
                    case PROMOTIONS_OFFER_PRODUCT:
                        $match->set_type($match::OFFER_PRODUCTID);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        // cannot be cumulative (its a new line item)
                        break;

                    case PROMOTIONS_OFFER_PRODUCTSKU:
                        $match->set_type($match::OFFER_PRODUCTSKU);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        // cannot be cumulative (its a new line item)
                        break;

                    case PROMOTIONS_OFFER_PERCENT:
                        $match->set_type($match::OFFER_PERCENT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cumulative($promo->get_extra('allow_cumulative'));
                        break;

                    case PROMOTIONS_OFFER_DISCOUNT:
                        $match->set_type($match::OFFER_DISCOUNT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cumulative($promo->get_extra('allow_cumulative'));
                        break;

                    case PROMOTIONS_OFFER_PRODDISCOUNT:
                        $match->set_type($match::OFFER_PERCENT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        $match->set_cumulative($promo->get_extra('allow_cumulative'));
                        break;

                    case PROMOTIONS_OFFER_PRODAMOUNT:
                        $match->set_type($match::OFFER_DISCOUNT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        $match->set_cumulative($promo->get_extra('allow_cumulative'));
                        break;

                    default:
                        throw new \CmsException('Invalid offer type at this stage: ' . $promo->get_offer_type());
                        die('ignored');
                        // an invalid offer type is just ignored.
                        break;
                }

                $matches[] = $match;
            }
        }
        if (count($matches))
        {
            return $matches;
        }
    }

    /**
     * test if the product (or sku) matches the offer part
     * of a promotion, and if the cart contents match the conditions
     * of the offer.
     * Stops after the first match.
     *
     * @return \EcommerceExt\Promotion\promotion_match
     */
    public function find_offer_match()
    {
        // converts a ecomm type to a promotion type
        $type = \EcPromotions\promotion::TYPE_CHECKOUT;
        switch ($this->get_promo_type())
        {
            case self::TYPE_INSTANT:
                $type = promotion::TYPE_INSTANT;
                break;
            case self::TYPE_CHECKOUT:
                $type = \EcPromotions\promotion::TYPE_CHECKOUT;
                break;
            default:
                throw new \CmsInvalidDataException('Unknown promotion type in ' . __CLASS__);
        }

        $promos = \EcPromotions\promotion::load_all_by_type($type);
        $match = null;
        foreach ($promos as $promo)
        {
            // the promo tester tests a single promotion against the item being tested.
            $tester = new promo_tester($promo);
            if ($this->get_sku())
            {
                $tester->set_sku($this->get_sku());
            }
            $tester->set_ignore_discounted($this->get_ignore_discounted());
            $tester->set_product($this->get_product());
            $tester->set_cart($this->get_cart());
            $tester->set_coupons($this->_coupons);
            $match_idx = - 1;
            if ($tester->find_offer_match() && ($this->get_skip_cart() || $tester->find_cart_match($match_idx)))
            {
                // so far, so good... offer matches this item... and maybe even the cart matches the conditions.
                $match = new Promotion\promotion_match();
                switch ($promo->get_offer_type())
                {
                    case PROMOTIONS_OFFER_PRODUCT:
                        // free product by id
                        $match->set_type($match::OFFER_PRODUCTID);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        break;

                    case PROMOTIONS_OFFER_PRODUCTSKU:
                        // free product by sku
                        $match->set_type($match::OFFER_PRODUCTSKU);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        break;

                    case PROMOTIONS_OFFER_PRODAMOUNT:
                        // discount applicable product by fixed amount
                        $match->set_type($match::OFFER_DISCOUNT);
                        $match->set_discount_amt($promo->get_offer_data());
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        break;

                    case PROMOTIONS_OFFER_PRODDISCOUNT:
                        // discount matching products by a percentage
                        $match->set_type($match::OFFER_PERCENT);
                        $match->set_discount_amt($promo->get_offer_data());
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        $match->set_cart_idx($match_idx);
                        // note, no cart index
                        break;

                    case PROMOTIONS_OFFER_PERCENT:
                        // percentage off order total
                        $match->set_type($match::OFFER_PERCENT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        // no cart index.
                        break;

                    case PROMOTIONS_OFFER_DISCOUNT:
                        // subtract amount from order total
                        $match->set_type($match::OFFER_DISCOUNT);
                        $match->set_val($promo->get_offer_data());
                        $match->set_promo($promo->get_id());
                        $match->set_name($promo->get_name());
                        // no cart index.
                        break;

                    case PROMOTIONS_OFFER_SAMEPRODUCT:
                        die('not done');
                        break;

                    default:
                        // an invalid offer type is just ignored.
                        break;
                }
            }
        }

        return $match;
    }

} // end of class

#
# EOF
#
?>
