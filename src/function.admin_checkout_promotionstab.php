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
// initialize
//

//
// get the data
//
$query = 'SELECT * FROM ' . PROMOTIONS_TABLE . ' WHERE type = ? ORDER BY item_order ASC';
$data = $db->GetArray($query, array(promotion::TYPE_CHECKOUT));
if (is_array($data))
{
    for ($i = 0; $i < count($data); $i ++)
    {
        $rec = &$data[$i];
        $rec['edit_url'] = $this->create_url($id, 'admin_addpromo', $returnid,
                                                array('promoid' => $rec['id']));
        $rec['edit_link'] = $this->CreateImageLink($id, 'admin_addpromo', $returnid,
                                        $this->Lang('edit'), 'icons/system/edit.gif', array('promoid' => $rec['id']));
        $rec['delete_url'] = $this->create_url($id, 'admin_delpromo', $returnid, array('promoid' => $rec['id']));
        $rec['delete_link'] = $this->CreateImageLink($id, 'admin_delpromo', $returnid,
                                        $this->Lang('delete'), 'icons/system/delete.gif', array('promoid' => $rec['id']),
                                            '', $this->Lang('really_delete_promotion'));
        $rec['start_date_ut'] = \xt_utils::unix_time($rec['start_date']);
        $rec['end_date_ut'] = \xt_utils::unix_time($rec['end_date']);
    }
}

//
// give everything to smarty
//
$smarty->assign('addlink', $this->CreateImageLink($id, 'admin_addpromo', $returnid, $this->Lang('prompt_addpromo'),
                                                    'icons/system/newobject.gif', array('type' => promotion::TYPE_CHECKOUT),
                                                    '', '', false));
if (is_array($data))
{
    $smarty->assign('promotions', $data);
}

//
// display the tempalte
//
echo $this->ProcessTemplate('admin_checkout_promotionstab.tpl');

#
# EOF
#
?>
