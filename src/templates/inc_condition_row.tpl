{* template for condition rows *}
{assign var="condtype" value="__INVALID__"}
{assign var="conddata0" value="__INVALID__"}
{assign var="conddata1" value="__INVALID__"}
{if isset($cond)}
  {assign var="condtype" value=$cond->get_cond_type()}
  {$tmp=$cond->get_data()}
  {if is_array($tmp)}
    {$conddata0=$tmp[0]}
    {$conddata1=$tmp[1]}
  {else}
    {$conddata0=$tmp}
  {/if}
{/if}

<div id="condition_row_tmpl" class='condition_row' style="display: none;">
  <input class="saved_condtype" type="hidden" name="{$actionid}saved_condtype[]" value="{$condtype}"/>
  <input class="saved_conddata0" type="hidden" name="{$actionid}saved_conddata[]" value="{$conddata0}"/>
  <input class="saved_conddata1" type="hidden" name="{$actionid}saved_conddata[]" value="{$conddata1}"/>
  <table style="padding: 1px; width: 100%;">
    <tr>
      <td>
        <span class='disp_condtype'>{$mod->Lang($condtype)}</span>
      </td>
      <td>
        <span class='disp_conddata0'>{$conddata0}</span> -- <span class="disp_conddata1">{$conddata1}</span></span>
      </td>
      <td>
        <a class="delcond" href="#" title="{$mod->Lang('delete')}">{SmartImage src='icons/system/delete.gif' alt=$mod->Lang('delete')}</a>
      </td>
    </tr>
  </table>
</div>