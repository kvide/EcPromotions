{literal}
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery('.hidable').hide();
  jQuery('#'+'{/literal}{$dflt_offer_type}{literal}').show();
  jQuery('#offertype').change(function(){
    var val = '#'+jQuery(this).val();
    jQuery('.hidable').hide();
    jQuery(val).show();
  });
});
</script>
{/literal}

{$formstart}
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_dflt_promotion_period')}:</p>
  <p class="pageinput">
    {capture assign='tmp'}{$actionid}promotion_period{/capture}
    {html_options name=$tmp options=$promotion_periods selected=$dflt_promotion_period}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_dflt_offer_type')}:</p>
  <p class="pageinput">
    {capture assign='tmp'}{$actionid}offer_type{/capture}
    <select name="{$tmp}" id="offertype">
    {html_options options=$offer_types selected=$dflt_offer_type}
    </select>
  </p>
</div>

<div class="pageoverflow hidable" id="PROMOTIONS_OFFER_PERCENT">
 <p class="pagetext">{$mod->Lang('prompt_percentage_discount')}:</p>
 <p class="pageinput">
   <input type="text" name="{$actionid}offer_percent" size="5" maxlength="5" value="{$dflt_offer_data}"/>
 </p>
</div>
<div class="pageoverflow hidable" id="PROMOTIONS_OFFER_DISCOUNT">
 <p class="pagetext">{$mod->Lang('prompt_price_discount')}:</p>
 <p class="pageinput">
   {$currency_symbol}<input type="text" name="{$actionid}offer_price" size="5" maxlength="5" value="{$dflt_offer_data}"/>
 </p>
</div>
<div class="pageoverflow hidable" id="PROMOTIONS_OFFER_PRODUCT">
{* <p class="pagetext">{$mod->Lang('prompt_free_product')}:</p>
 <p class="pageinput">&nbsp;</p>*}
</div>

{if $dflt_offer_type == 'PROMOTIONS_OFFER_PERCENT'}
{elseif $dflt_offer_type == 'PROMOTIONS_OFFER_DISCOUNT'}
{else}
{/if}


<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_image_dir')}</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}image_dir" value="{$image_dir}" size="50" maxlength="255"/>
    <br/>
    {$mod->Lang('info_image_dir')}
  </p>
</div>

{* promotional messages *}
<fieldset>
<legend>{$mod->Lang('lbl_promo_messages')}:&nbsp;</legend>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_error_invalid')}</p>
  <p class="pageinput">
    <textarea rows="3" cols="50" name="{$actionid}error_invalid">{$error_invalid_code}</textarea>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_error_empty')}</p>
  <p class="pageinput">
    <textarea rows="3" cols="50" name="{$actionid}error_empty">{$error_empty_code}</textarea>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_msg_valid_code')}</p>
  <p class="pageinput">
    <textarea rows="3" cols="50" name="{$actionid}msg_valid">{$msg_valid_code}</textarea>
  </p>
</div>

</fieldset>

<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}setaspromo" value="{$mod->Lang('setaspromo')}"/>
  </p>
</div>
{$formend}