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

final class condition_tests
{

    private function __construct()
    {
    }

    /**
     * Test this condition to see if it is an FEU group condition
     * and if the user id specified (or automatically determined)
     * matches
     *
     * return TRUE on match, FALSE otherwise
     */
    public static function test_feu_uid(promotion_condition $condition, $feu_uid = - 1)
    {
        if ($condition->get_cond_type() != PROMOTIONS_COND_FEU)
        {
            return FALSE;
        }

        $feu = \cms_utils::get_module('MAMS');
        if (! $feu)
        {
            return FALSE;
        }
        if ($feu_uid == - 1)
        {
            $feu_uid = $feu->LoggedInId();
        }
        if ($feu_uid <= 0)
        {
            return FALSE;
        }

        // build a list of member group names
        $allgroups = $feu->GetGroupList();
        $allgroups = array_flip($allgroups);
        $member_of = $feu->GetMemberGroupsArray($feu_uid);
        if (! $member_of)
        {
            return FALSE;
        }
        $member_of = \xt_array::extract_field($member_of, 'groupid');
        $member_group_names = array();
        foreach ($member_of as $mgid)
        {
            $member_group_names[] = $allgroups[$mgid];
        }

        // get the value from the condition, as an array of trimmed group names
        $data = explode(',', $condition->get_data());
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

        return FALSE;
    }

} // end of class

#
# EOF
#
?>
