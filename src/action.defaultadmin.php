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

echo $this->StartTabHeaders();
if ($this->CheckPermission(\EcPromotions::MAIN_PERM))
{
    echo $this->SetTabHeader('checkout_promotions', $this->Lang('lbl_checkout_promotions'));
    echo $this->SetTabHeader('instant_promotions', $this->Lang('lbl_instant_promotions'));
}
if ($this->CheckPermission('Modify Templates'))
{
    echo $this->SetTabHeader('couponform', $this->Lang('lbl_couponform_templates'));
    echo $this->SetTabHeader('dflt_templates', $this->Lang('lbl_default_templates'));
}
if ($this->CheckPermission('Modify Site Preferences'))
{
    echo $this->SetTabHeader('settings', $this->Lang('lbl_settings'));
}
echo $this->EndTabHeaders();

echo $this->StartTabContent();
if ($this->CheckPermission(\EcPromotions::MAIN_PERM))
{
    echo $this->StartTab('checkout_promotions', $params);
    include (dirname(__FILE__) . '/function.admin_checkout_promotionstab.php');
    echo $this->EndTab();

    echo $this->StartTab('instant_promotions', $params);
    include (dirname(__FILE__) . '/function.admin_instant_promotionstab.php');
    echo $this->EndTab();
}
if ($this->CheckPermission('Modify Templates'))
{
    echo $this->StartTab('couponform', $params);
    include (dirname(__FILE__) . '/function.admin_couponformtab.php');
    echo $this->EndTab();

    echo $this->StartTab('dflt_templates', $params);
    include (dirname(__FILE__) . '/function.admin_dflt_templates_tab.php');
    echo $this->EndTab();
}
if ($this->CheckPermission('Modify Site Preferences'))
{
    echo $this->StartTab('settings', $params);
    include (dirname(__FILE__) . '/function.admin_settingstab.php');
    echo $this->EndTab();
}

echo $this->EndTabContent();

#
# EOF
#
?>
