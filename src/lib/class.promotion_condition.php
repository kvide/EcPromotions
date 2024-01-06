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

// require_once(dirname(__FILE__) . '/class.promo_ops.php');
class promotion_condition
{

    private $_id;
    private $_promotion_id;
    private $_cond_type;
    private $_data;

    public static function get_types_array($type = promotion::TYPE_CHECKOUT)
    {
        switch ($type)
        {
            case promotion::TYPE_INSTANT:
                return array(
                    PROMOTIONS_COND_FEU,
                    PROMOTIONS_COND_PRODID,
                    PROMOTIONS_COND_PRODCAT,
                    PROMOTIONS_COND_PRODHIER,
                    PROMOTIONS_COND_COUPON,
                    PROMOTIONS_COND_PRODSKU,
                    PROMOTIONS_COND_BULKPURCHASE
                );
            case promotion::TYPE_CHECKOUT:
                return array(
                    PROMOTIONS_COND_FEU,
                    PROMOTIONS_COND_SUBTOTAL,
                    PROMOTIONS_COND_PRODID,
                    PROMOTIONS_COND_PRODCAT,
                    PROMOTIONS_COND_PRODHIER,
                    PROMOTIONS_COND_COUPON,
                    PROMOTIONS_COND_WEIGHT,
                    PROMOTIONS_COND_PRODSKU,
                    PROMOTIONS_COND_BULKPURCHASE
                );
        }
    }

    public function get_id()
    {
        return $this->_id;
    }

    public function set_id($id)
    {
        $this->_id = $id;
    }

    public function get_promotion_id()
    {
        return $this->_promotion_id;
    }

    public function set_promotion_id($id)
    {
        $this->_promotion_id = $id;
    }

    public function get_cond_type()
    {
        return $this->_cond_type;
    }

    public function set_cond_type($type)
    {
        if ($type == PROMOTIONS_COND_FEU || $type == PROMOTIONS_COND_SUBTOTAL
            || $type == PROMOTIONS_COND_WEIGHT || $type == PROMOTIONS_COND_COUPON
            || $type == PROMOTIONS_COND_BULKPURCHASE || $type == PROMOTIONS_COND_PRODID
            || $type == PROMOTIONS_COND_PRODCAT || $type == PROMOTIONS_COND_PRODSKU
            || $type == PROMOTIONS_COND_PRODHIER)
        {
            $this->_cond_type = $type;
        }
    }

    public function get_data()
    {
        return $this->_data;
    }

    public function set_data($data)
    {
        $this->_data = $data;
    }

}

#
# EOF
#
?>
