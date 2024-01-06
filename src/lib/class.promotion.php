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

class promotion
{

    private $_id;
    private $_name;
    private $_description;
    private $_image;
    private $_created;
    private $_start_date;
    private $_end_date;
    private $_offer_type;
    private $_offer_data;
    private $_conditions;
    private $_item_order;
    private $_type;
    private $_extra;
    private $_owner;
    private static $_type_cache;

    const TYPE_INSTANT = 'promo_type_instant';
    const TYPE_CHECKOUT = 'promo_type_checkout';

    public function __construct()
    {
        $this->_type = self::TYPE_CHECKOUT;
    }

    static public function get_types_array()
    {
        return array(
            self::TYPE_INSTANT,
            self::TYPE_CHECKOUT
        );
    }

    static public function get_offer_array($type = self::TYPE_CHECKOUT)
    {
        switch ($type)
        {
            case self::TYPE_INSTANT:
                return array(
                    PROMOTIONS_OFFER_PRODUCT,
                    PROMOTIONS_OFFER_PRODUCTSKU,
                    PROMOTIONS_OFFER_PRODDISCOUNT,
                    PROMOTIONS_OFFER_PRODAMOUNT
                );

            case self::TYPE_CHECKOUT:
                return array(
                    PROMOTIONS_OFFER_PERCENT,
                    PROMOTIONS_OFFER_DISCOUNT,
                    PROMOTIONS_OFFER_PRODUCT,
                    PROMOTIONS_OFFER_PRODUCTSKU,
                    PROMOTIONS_OFFER_PRODDISCOUNT,
                    PROMOTIONS_OFFER_PRODAMOUNT
                );
        }
    }

    static public function get_offer_extra_map()
    {
        $res = array();
        $res[PROMOTIONS_OFFER_PRODUCT] = array(
            array(
                'name' => 'only_one',
                'type' => 'checkbox',
                'label' => 'lbl_onlyone'
            )
        );
        $res[PROMOTIONS_OFFER_PRODUCTSKU] = array(
            array(
                'name' => 'only_one',
                'type' => 'checkbox',
                'label' => 'lbl_onlyone'
            )
        );

        return $res;
    }

    public function get_id()
    {
        return $this->_id;
    }

    public function set_id($val)
    {
        $this->_id = $val;
    }

    public function get_name()
    {
        return $this->_name;
    }

    public function set_name($val)
    {
        $this->_name = $val;
    }

    public function get_owner()
    {
        return $this->_owner;
    }

    public function set_owner($owner_id)
    {
        $owner_id = (int) $owner_id;
        if ($owner_id < 1)
        {
            $owner_id = null;
        }
        $this->_owner = $owner_id;
    }

    public function get_description()
    {
        return $this->_description;
    }

    public function set_description($val)
    {
        $this->_description = $val;
    }

    public function get_image()
    {
        return $this->_image;
    }

    public function set_image($val)
    {
        if (empty($val) || (is_int($val) && ($val < 1)))
        {
            return FALSE;
        }
        $this->_image = $val;
    }

    public function get_type()
    {
        return $this->_type;
    }

    public function set_type($val)
    {
        switch ($val)
        {
            case self::TYPE_INSTANT:
            case self::TYPE_CHECKOUT:
                $this->_type = $val;
                break;
        }
    }

    public function get_created()
    {
        return $this->_created;
    }

    public function set_created($val)
    {
        $this->_created = $val;
    }

    public function get_item_order()
    {
        return $this->_item_order;
    }

    public function get_start_date()
    {
        return $this->_start_date;
    }

    public function set_start_date($val)
    {
        $this->_start_date = $val;
    }

    public function get_end_date()
    {
        return $this->_end_date;
    }

    public function set_end_date($val)
    {
        $this->_end_date = $val;
    }

    public function get_offer_type()
    {
        return $this->_offer_type;
    }

    public function set_offer_type($val)
    {
        $this->_offer_type = $val;
    }

    public function get_offer_data()
    {
        return $this->_offer_data;
    }

    public function set_extra($key, $value)
    {
        if (! is_array($this->_extra))
        {
            $this->_extra = array();
        }
        $this->_extra[$key] = $value;
    }

    public function get_extra($key)
    {
        if (is_array($this->_extra) && isset($this->_extra[$key]))
        {
            return $this->_extra[$key];
        }
    }

    public function unset_extra($key)
    {
        if (is_array($this->_extra) && isset($this->_extra[$key]))
        {
            unset($this->_extra[$key]);
        }
    }

    public function clear_extra()
    {
        if (is_array($this->_extra))
        {
            $this->extra = null;
        }
    }

    public function set_offer_data($data)
    {
        $this->_offer_data = $data;
    }

    public function &get_conditions()
    {
        return $this->_conditions;
    }

    public function add_condition(&$condition)
    {
        if (! is_a($condition, '\\Promotions\\promotion_condition'))
        {
            return FALSE;
        }
        if (! is_array($this->_conditions))
        {
            $this->_conditions = array();
        }
        $this->_conditions[] = $condition;
    }

    public function del_condition($idx)
    {
        if (is_array($this->_conditions) && count($this->_conditions) > $idx && $idx > 0)
        {
            $tmp = array();
            for ($i = 0; $i < count($this->_conditions); $i ++)
            {
                if ($i == $idx)
                {
                    continue;
                }
                $tmp[] = $this->_conditions;
            }
            $this->_conditions = $tmp;
        }
    }

    public function del_conditions()
    {
        $this->_conditions = null;
    }

    public function &get_condition($idx)
    {
        if (! is_array($this->_conditions) || (count($this->_conditions) < $idx) || ($idx < 0))
        {
            $tmp = FALSE;
            return $tmp;
        }

        return $this->_conditions[$idx];
    }

    public function count_conditions()
    {
        if (empty($this->_conditions))
        {
            return 0;
        }

        return count($this->_conditions);
    }

    public function save()
    {
        if ($this->_id)
        {
            return $this->_update();
        }

        return $this->_insert();
    }

    public function delete()
    {
        $db = cmsms()->GetDb();
        $query = 'DELETE FROM ' . PROMOTIONS_COND_TABLE . ' WHERE promotion_id = ?';
        $res = $db->Execute($query, array($this->_id));
        // no error if there are no conditions.

        $query = 'DELETE FROM ' . PROMOTIONS_TABLE . ' WHERE id = ?';
        $res = $db->Execute($query, array($this->_id));
        if (! $res)
        {
            throw new \CmsException('Error deleting promotion');
        }

        $query = 'UPDATE ' . PROMOTIONS_TABLE . ' SET item_order = item_order - 1 WHERE item_order > ? AND type = ?';
        $res = $db->Execute($query, array($this->_item_order, $this->_type));
        if (! $res)
        {
            throw new \CmsException('Error deleting promotion (updating item order)');
        }

        return TRUE;
    }

    /**
     * Delete conditions from the database for this promotion
     */
    private function _del_conditions()
    {
        if (! $this->get_id())
        {
            return FALSE;
        }

        $db = cmsms()->GetDb();
        $query = 'DELETE FROM ' . PROMOTIONS_COND_TABLE . ' WHERE promotion_id = ?';
        $dbr = $db->Execute($query, array($this->get_id()));
        if (! $dbr)
        {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Insert a condition into the database for this promotion
     */
    private function _insert_condition(promotion_condition $cond)
    {
        if (! $this->get_id())
        {
            return FALSE;
        }
        $cond->set_promotion_id($this->get_id());

        $_store_data = function ($tmp)
        {
            if (is_array($tmp))
            {
                if (count($tmp) == 1)
                {
                    return $tmp[0];
                }
                return serialize($tmp);
            }
            else if (is_object($tmp))
            {
                return serialize($tmp);
            }
            return $tmp;
        };

        $db = cmsms()->GetDb();
        $query = 'INSERT INTO ' . PROMOTIONS_COND_TABLE . ' (promotion_id,cond_type,data) VALUES (?,?,?)';
        $dbr = $db->Execute($query, array(
            $cond->get_promotion_id(),
            $cond->get_cond_type(),
            $_store_data($cond->get_data())
        ));
        if (! $dbr)
        {
            throw new \CmsException('SQL Error: ' . $db->sql . ' -- ' . $db->ErrorMsg());
        }

        return TRUE;
    }

    private function _update()
    {
        if (! $this->get_id())
        {
            return FALSE;
        }

        $db = cmsms()->GetDb();
        $query = 'UPDATE ' . PROMOTIONS_TABLE . '
                  SET name = ?, type = ?, description = ?, image = ?, start_date = ?, end_date = ?, offer_type = ?,'
                    .' offer_data = ?, extra = ?, owner = ?
                  WHERE id = ?';
        $dbr = $db->Execute($query, array(
            $this->get_name(),
            $this->get_type(),
            $this->get_description(),
            $this->get_image(),
            \xt_utils::db_time($this->get_start_date()),
            \xt_utils::db_time($this->get_end_date()),
            $this->get_offer_type(),
            $this->get_offer_data(),
            serialize($this->_extra),
            $this->_owner,
            $this->get_id()
        ));
        if (! $dbr)
        {
            throw new \CmsException('SQL Error: ' . $db->sql . ' -- ' . $db->ErrorMsg());
        }

        // insert the conditions
        self::_del_conditions();
        for ($i = 0; $i < $this->count_conditions(); $i ++)
        {
            $cond = $this->get_condition($i);
            if (! $cond)
            {
                continue;
            }
            self::_insert_condition($cond);
        }

        return $this->get_id();
    }

    private function _insert()
    {
        $db = cmsms()->GetDb();
        $query = 'SELECT MAX(item_order) FROM ' . PROMOTIONS_TABLE . ' WHERE type = ?';
        $item_order = $db->GetOne($query, array($this->get_type()));
        if ($item_order == '')
        {
            $item_order = 0;
        }
        $item_order ++;

        $now = $db->DbTimeStamp(time());
        $query = 'INSERT INTO ' . PROMOTIONS_TABLE . "
                  (name,type,description,image,created,start_date,end_date,offer_type,offer_data,item_order,extra,owner)
                  VALUES (?,?,?,?,$now,?,?,?,?,?,?,?)";
        $dbr = $db->Execute($query, array(
            $this->get_name(),
            $this->get_type(),
            $this->get_description(),
            $this->get_image(),
            \xt_utils::db_time($this->get_start_date()),
            \xt_utils::db_time($this->get_end_date()),
            $this->get_offer_type(),
            $this->get_offer_data(),
            $item_order,
            serialize($this->_extra),
            $this->_owner
        ));
        if (! $dbr)
        {
            throw new \CmsException('SQL Error: ' . $db->sql . ' -- ' . $db->ErrorMsg());
        }
        $this->_id = $db->Insert_ID();

        // insert the conditions
        for ($i = 0; $i < $this->count_conditions(); $i ++)
        {
            $cond = $this->get_condition($i);
            if (! $cond)
            {
                continue;
            }
            self::_insert_condition($cond);
        }

        return $this->_id;
    }

    public function check_valid()
    {
        $st = \xt_utils::unix_time($this->get_start_date());
        $et = \xt_utils::unix_time($this->get_end_date());
        $cr = \xt_utils::unix_time($this->get_created());

        if ($st < $cr)
        {
            return FALSE;
        }
        if ($et <= $st)
        {
            return FALSE;
        }
        if (empty($this->_name))
        {
            return FALSE;
        }
        if (empty($this->_offer_data))
        {
            return FALSE;
        }

        return TRUE;
    }

    private static function _load_from_data($data)
    {
        $res = null;
        if (! is_array($data) || count($data) == 0)
        {
            return $res;
        }
        if (! isset($data['raw_conditions']))
        {
            return $res;
        }

        $promo = new promotion();
        foreach ($data as $key => $value)
        {
            if ($key == 'raw_conditions')
            {
                continue;
            }
            $m = '_' . $key;
            if ($key == 'extra')
            {
                $value = unserialize($value);
            }
            $promo->$m = $value;
        }

        foreach ($data['raw_conditions'] as $row)
        {
            $cond_ob = new promotion_condition();
            foreach ($row as $key => $value)
            {
                $fun = 'set_' . $key;
                $cond_ob->$fun($value);
            }
            $promo->add_condition($cond_ob);
        }

        return $promo;
    }

    public static function load_by_id($id)
    {
        if (($id <= 0) || ! is_numeric($id))
        {
            throw new \CmsException('Cannot load invalid promotion id ' . $id);
        }

        $db = cmsms()->GetDb();
        $query = 'SELECT * FROM ' . PROMOTIONS_TABLE . ' WHERE id = ?';
        $row = $db->GetRow($query, array((int) $id));
        if (! $row)
        {
            throw new \CmsException('Problem loading promotion with id ' . $id);
        }

        // load conditions.
        $query = 'SELECT * FROM ' . PROMOTIONS_COND_TABLE . ' WHERE promotion_id = ?';
        $data = $db->GetArray($query, array((int) $id));
        for ($i = 0; $i < count($data); $i ++)
        {
            $tmp = @unserialize($data[$i]['data']);
            if ($tmp)
            {
                $data[$i]['data'] = $tmp;
            }
        }
        $row['raw_conditions'] = $data;

        return self::_load_from_data($row);
    }

    public static function load_by_owner($owner_id)
    {
        $owner_id = (int) $owner_id;
        if ($owner_id < 1)
        {
            throw new \LogicException('Invalid owner id specified for ' . __METHOD__);
        }

        $db = \CmsApp::get_instance()->GetDb();
        $sql = 'SELECT * FROM ' . PROMOTIONS_TABLE . ' WHERE owner = ? ORDER BY item_order ASC';
        $promolist = $db->GetArray($sql, [$owner_id]);
        if (! $promolist)
        {
            return;
        }

        $idlist = \xt_array::extract_field($promolist, 'id');
        $sql = 'SELECT * FROM ' . PROMOTIONS_COND_TABLE . ' WHERE promotion_id IN ('
                . implode(',', $idlist) . ') ORDER BY promotion_id ASC';
        $condlist = $db->GetArray($sql);

        $out = [];
        foreach ($promolist as $row)
        {
            foreach ($condlist as $row2)
            {
                if ($row2['promotion_id'] < $row['id'])
                {
                    continue;
                }
                if ($row2['promotion_id'] > $row['id'])
                {
                    break;
                }

                $tmp = @unserialize($row2['data']);
                if ($tmp)
                {
                    $row2['data'] = $tmp;
                }

                if (! isset($row['raw_conditions']))
                {
                    $row['raw_conditions'] = array();
                }
                $row['raw_conditions'][] = $row2;
            }
            $obj = self::_load_from_data($row);
            if ($obj)
            {
                $out[] = $obj;
            }
        }

        return $out;
    }

    public static function load_all_by_type($type, $onlyvalid = TRUE)
    {
        switch ($type)
        {
            case promotion::TYPE_INSTANT:
            case promotion::TYPE_CHECKOUT:
                if (isset(self::$_type_cache[$type]))
                {
                    return self::$_type_cache[$type];
                }

                $db = cmsms()->GetDb();
                $query = 'SELECT * FROM ' . PROMOTIONS_TABLE . ' WHERE type = ?';
                if ($onlyvalid)
                {
                    $date = $db->DbTimeStamp(time());
                    $query .= " AND (start_date <= $date) AND (end_date >= $date)";
                }
                $query .= ' ORDER BY item_order ASC';
                $dbr = $db->Execute($query, array($type));
                $ids = array();
                while (! $dbr->EOF)
                {
                    $row = $dbr->fields;
                    if (! in_array($row['id'], $ids))
                    {
                        $ids[] = (int) $row['id'];
                    }
                    $dbr->MoveNext();
                }
                $dbr->MoveFirst();

                $dbr2 = null;
                if (is_array($ids) && count($ids))
                {
                    $query = 'SELECT * FROM ' . PROMOTIONS_COND_TABLE . ' WHERE promotion_id IN ('
                                . implode(',', $ids) . ') ORDER BY promotion_id ASC';
                    $dbr2 = $db->GetArray($query);
                }

                $out = array();
                while (! $dbr->EOF)
                {
                    $row = $dbr->fields;
                    foreach ($dbr2 as $row2)
                    {
                        if ($row2['promotion_id'] < $row['id'])
                        {
                            continue;
                        }
                        if ($row2['promotion_id'] > $row['id'])
                        {
                            break;
                        }

                        $tmp = @unserialize($row2['data']);
                        if ($tmp)
                        {
                            $row2['data'] = $tmp;
                        }

                        if (! isset($row['raw_conditions']))
                        {
                            $row['raw_conditions'] = array();
                        }
                        $row['raw_conditions'][] = $row2;
                    }
                    $obj = self::_load_from_data($row);
                    if ($obj)
                    {
                        $out[] = $obj;
                    }
                    $dbr->MoveNext();
                }

                if (! is_array(self::$_type_cache))
                {
                    self::$_type_cache = array();
                }
                self::$_type_cache[$type] = $out;
                return $out;
                break;

            default:
                throw new \CmsInvalidDataException('Invalid promotion type ' . $type);
        }
    }

} // class

#
# EOF
#
?>
