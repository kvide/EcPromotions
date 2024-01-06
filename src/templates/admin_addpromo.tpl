{if $promo->get_id() == ''}
  <h3>{$mod->Lang('lbl_add_promo')}</h3>
{else}
  <h3>{$mod->Lang('lbl_edit_promo')}</h3>
{/if}
<h4>{$mod->Lang('type')}: {$mod->Lang($promotype)} ({$promotype})</h4>

<script type='text/javascript'>
// <![CDATA[
var conditions = {};
{foreach $conditiontypes as $key => $rec}
conditions.{$key} = {
  name: '{$rec.name}',
  label: '{$rec.label}',
  fields: [ {for $i = 1 to count($rec.fields)}{$tmp=$rec.fields[$i-1]}'{$tmp}',{/for} ]
}
{/foreach}

var offer_prompts = new Array();
{foreach from=$offertypes key='name' item='value'}
{capture assign='tmp'}promptoffer_{$name}{/capture}
offer_prompts["{$name}"] = "{$mod->Lang($tmp)}";
{/foreach}
var errors = new Array();
errors['no_value'] = "{$mod->Lang('error_specify_value')}";
errors['already_prod'] = "{$mod->Lang('error_already_product_cond')}";
errors['duplicate_type'] = "{$mod->Lang('error_duplicate_condition')}";
errors['no_name'] = "{$mod->Lang('error_promotion_name')}";
errors['no_offer_data'] = "{$mod->Lang('error_no_offer_data')}";
errors['no_conditions'] = "{$mod->Lang('warning_no_conditions')}";

function isNumeric(form_value) {
  if (form_value.match(/^\d+$/) == null) return false;
  return true;
}

function in_array(needle,haystack) {
  for( i = 0; i < haystack.length; i++ ) {
    if( haystack[i] == needle ) return true;
  }
  return false;
}

function get_conditions() {
  var conds = [];
  $('#conddata div.condition_data').each(function(){
    var type = $(this).find('.saved_condtype').val();
    var data = [];
    data.push($(this).find('.saved_conddata0').val());
    var v = $(this).find('.saved_conddata1').val();
    if( typeof v != 'undefined' && v.length > 0 ) data.push(v);
    conds[type] = data;
  });
  return conds;
}

function condition_invalid(type) {
  var conds = get_conditions();
  // pass one, look for duplicate types
  for( var ctype in conds ) {
    if( ctype == type ) return 'duplicate_type';
  }

  // pass two, look for duplicate product types
  for( var ctype in conds ) {
    if( ctype.match(/PROD/) && type.match(/PROD/) ) return 'already_prod';
  }
  return false;
}

function _add_condition(type,data)
{
  var _data_area = $('div#condition_template div.condition_data').clone();
  var _disp_area = $('div#condition_template tr.condition_row').clone();

  $('input.condtype',_data_area).val(type);
  $('td.disptype',_disp_area).html(conditions[type].label);
  if( data.length > 0 ) {
    $('input.conddata0',_data_area).val(data[0]);
    $('td.dispdata0',_disp_area).html(data[0]);
  }
  if( data.length > 1 ) {
    $('input.conddata1',_data_area).val(data[1]);
    $('.dispdata1',_disp_area).html(data[1]);
  }
  $('#conddata').append(_data_area);
  $('#condtable').append(_disp_area);
}

function _handle_initial_conditions()
{
  // handle initial conditions
  var tmp;
  {for $i = 1 to $promo->count_conditions()}
  {$cond=$promo->get_condition($i-1)}
  {$data=$cond->get_data()}
  tmp = [];
  {foreach $data as $one}
  tmp.push('{$one}');
  {/foreach}
  _add_condition('{$cond->get_cond_type()}',tmp);
  {/for}
  $('#condlist > .condition_row').show();
}

jQuery(document).ready(function(){
  _handle_initial_conditions();

  // handler for extra area
  jQuery('#extras').hide();
  jQuery('#linkextras').click(function(){
    jQuery('#extras').toggle();
    return false;
  });

  // setup initial condition list stuff.
  $(document).on('click','a.delcond',function(){
    var row = $(this).closest('tr.condition_row');
    var idx = $(row).index();
    $('div#conddata div.condition_data').eq(idx).remove();
    $(row).remove();
    return false;
  });

  // initialize the offer prompt
  type = jQuery('#offertype').val();
  $('#offerdata').html(offer_prompts[type]);
  $('#'+type).show();
  $('#offertype').change(function(){
    // offer type changed
    var type = jQuery('#offertype').val();
    $('#offerdata').html(offer_prompts[type]);
    $('.offer_extra').hide();
    $('#'+type).show();
  });

  // handle condition type changed
  $('#condtype').change(function(){
    // condition type changed
    var type = $('#condtype').val();
    var n = conditions[type].fields.length;
    type = conditions[type];
    $('div.conddata').hide();
    $('div.conddata input').attr('disabled','disabled');
    for( var i = 0; i < n; i++ ) {
      $('p#dataprompt'+i).html(type.fields[i]+':');
      $('div#div_conddata'+i+' input').removeAttr('disabled');
      $('div#div_conddata'+i).show();
    }
  });
  $('#condtype').trigger('change');

  // handle add condition clicked.
  $(document).on('click','#addbtn',function(){
    var type = $('#condtype').val();
    var n = conditions[type].fields.length;

    var condtype = $('#condtype').val();
    var data = [];
    var t = $('#conddata0').val();
    if( typeof t != 'undefined' && t.length > 0 ) data.push(t);
    t = $('#conddata1').val();
    if( typeof t != 'undefined' && t.length > 0 ) data.push(t);

    if( data.length != n ) {
      alert(errors['no_value']);
      return false;
    }

    if( condtype == 'PROMOTIONS_COND_PRODID' ) {
      if( parseInt(conddata) <= 0 ) {
        alert(errors['invalid_value']);
        return false;
      }
    }
    else if( condtype == 'PROMOTIONS_COND_SUBTOTAL' ) {
      // todo, error check here
    }
    error = condition_invalid(condtype);
    if( error ) {
      alert(errors[error]);
      return false;
    }

    _add_condition(condtype,data);
    return false;
  });

  // handleform submit
  jQuery('#submit').live('click',function(){
    var name = jQuery('#name').val();
    var offerdata = jQuery('#offer_data').val();
    if( name == '' ) {
      alert(errors['no_name']);
      return false;
    }
    if( offerdata == '' ) {
      alert(errors['no_offer_data']);
      return false;
    }
    // warn about no conditions.
    if( jQuery('div#conddata').children().size() == 0 ) {
      return confirm(errors['no_conditions']);
    }
    return true;
    // success
  });

});
// ]]>
</script>

<div id="condition_template" style="display: none;">
  <div class="condition_data">
    <input class="condtype"  type="hidden" readonly="readonly" name="{$actionid}saved_condtype[]"  size="50"/>
    <input class="conddata0" type="hidden" readonly="readonly" name="{$actionid}saved_conddata0[]" size="50"/>
    <input class="conddata1" type="hidden" readonly="readonly" name="{$actionid}saved_conddata1[]" size="50"/>
  </div>

  <table>
    <tr class="condition_row">
      <td class="disptype"></td>
      <td class="dispdata0"></td>
      <td class="dispdata1"></td>
      <td><a class="delcond" href="#">{SmartImage src='icons/system/delete.gif' alt=$mod->Lang('delete')}</a></td>
    </tr>
  </table>
</div>

{$formstart}

<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" id='submit' name="{$actionid}submit" value="{$mod->Lang('submit')}" />
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}" />
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_name')}:</p>
  <p class="pageinput">
    <input type="text" id='name' name="{$actionid}name" value="{$promo->get_name()}" size="80" maxlength="255" />
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_start_date')}:</p>
  <p class="pageinput">
    {capture assign='tmp'}{$actionid}start_date_{/capture}
    {html_select_date prefix=$tmp time=$promo->get_start_date() end_year="+5"}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_end_date')}:</p>
  <p class="pageinput">
    {capture assign='tmp'}{$actionid}end_date_{/capture}
    {html_select_date prefix=$tmp time=$promo->get_end_date() end_year="+5"}
  </p>
</div>

<fieldset>
  <legend>{$mod->Lang('prompt_offer')}:</legend>
  <div class="pageoverflow nooffset">
    <p class="pagetext nooffset">
      {$mod->Lang('prompt_offer_type')}:
    </p>
    <p class="pageinput nooffset">
      <select name="{$actionid}offer_type" id="offertype">
      {html_options options=$offertypes selected=$promo->get_offer_type()}
      </select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext nooffset" id="offerdata">
      {$mod->Lang('prompt_offer_data')}:
    </p>
    <p class="pageinput nooffsffet">
      <input id="offer_data" type="text" name="{$actionid}offer_data" size="10" maxlength="255" value="{$promo->get_offer_data()}"/>
    </p>
  </div>

  {if $promotype == 'promo_type_instant'}
    <div class="pagoverflow">
      <p class="pagetext">{$mod->Lang('prompt_allow_once')}:</p>
      <p class="pageinput">
        {xt_yesno_options prefix=$actionid name='extra_allow_once' selected=$promo->get_extra('allow_once')}
      </p>
    </div>
  {/if}

  {if $promotype == 'promo_type_checkout'}
    <div class="pagoverflow">
      <p class="pagetext">{$mod->Lang('prompt_allow_cumulative')}:</p>
      <p class="pageinput">
        {xt_yesno_options prefix=$actionid name='extra_allow_cumulative' selected=$promo->get_extra('allow_cumulative')}
      </p>
    </div>
  {/if}

  <div id-"extra_info_area">
  {assign var='map' value=$promo->get_offer_extra_map()}
  {foreach from=$map key='offertype' item='flds'}
    <div id="{$offertype}" class="offer_extra" style="display: none;">
      {foreach from=$flds item='fld'}
      <div class="pageoverflow">
        <p class="pagetext">{assign var='a' value=$fld.label}{$mod->Lang($a)}:</p>
        <p class="pageinput">
          {if $fld.type=='checkbox'}
            {assign var='b' value=$fld.name}
            <input type="checkbox" name="{$actionid}extra_{$b}" value="1" {if $promo->get_extra($b)}checked="checked"{/if}/>
          {/if}
        </p>
      </div>
      {/foreach}
    </div>
  {/foreach}
  </div>
</fieldset>

<fieldset class="pageoverflow">
  <legend>{$mod->Lang('prompt_conditions')}:&nbsp;</legend>
  <div id="conddata"></div>
  <table id="condtable" style="padding: 1px; width: 100%;"></table>
</fieldset>

<fieldset class="pageoverflow">
 <legend>{$mod->Lang('prompt_add_condition')}:</legend>
 <p class="information">{$mod->Lang('info_conditions')}</p>
 <div class="pageoverflow">
   <p class="pagetext">{$mod->Lang('prompt_condition_type')}:</p>
   <p class="pageinput">
     <select name="{$actionid}add_condtype" id="condtype">
     {foreach $conditiontypes as $key => $rec}
       <option value="{$key}">{$rec.label}</option>
     {/foreach}
     </select>
   </p>
 </div>

 <div id="div_conddata0" class="pageoverflow conddata">
   <p class="pagetext" id="dataprompt0">{$mod->Lang('prompt_condition_data')}:</p>
   <p class="pageinput">
     <input id="conddata0" type="text" name="add_conddata1" size="30"/>
   </p>
 </div>

 <div id="div_conddata1" class="pageoverflow conddata">
   <p class="pagetext" id="dataprompt1">{$mod->Lang('prompt_condition_data2')}:</p>
   <p class="pageinput">
     <input id="conddata1" type="text" name="add_conddata2" size="30"/>
   </p>
 </div>

 <div class="pageoverflow">
   <p class="pagetext"></p>
   <p class="pageinput">
     <input type="submit" id="addbtn" name="add" value="{$mod->Lang('add')}"/>
   </p>
 </div>
</fieldset>


{$formend}