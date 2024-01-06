{* coupon form template *}
{$formstart}
<fieldset>
<legend>{$Promotions->Lang('prompt_coupon_code')}</legend>

<div style="text-align: center;">
  {if isset($error)}<p style="color: red;">{$error}</p>{/if}
  {if isset($msg)}<p style="color: green;">{$msg}</p>{/if}

  <p>{$mod->Lang('prompt_code')}:&nbsp;<input type="text" name="{$actionid}promo_code" size="12" maxlength="10" value="{$code}"/></p>
  <p>
    <input type="submit" name="{$actionid}promo_submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}promo_clear" value="{$mod->Lang('clear_coupons')}"/>
  </p>

  {if isset($coupons)}
  <p>{$mod->Lang('entered_coupons')}:</p>
  <ul>
  {foreach from=$coupons item='item' name='coupons'}
    <li>{$smarty.foreach.coupons.iteration}: {$item}</li>
  {/foreach}
  </ul>
  {/if}  
</div>

</fieldset>
{$formend}