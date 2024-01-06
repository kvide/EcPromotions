{* promotions tab template *}
<div class="information">{$mod->Lang('info_checkout_promotions')}</div>
{if isset($promotions)}
<table class="pagetable cms_sortable tablesorter" cellspacing="0">
  <thead>
   <tr>
     <th>{$mod->Lang('prompt_id')}</th>
     <th>{$mod->Lang('prompt_name')}</th>
     <th>{$mod->Lang('prompt_start_date')}</th>
     <th>{$mod->Lang('prompt_end_date')}</th>
     <th class="pageicon {literal}{sorter: false}{/literal}">&nbsp;</th>
     <th class="pageicon {literal}{sorter: false}{/literal}">&nbsp;</th>
     <th class="pageicon {literal}{sorter: false}{/literal}">&nbsp;</th>
     <th class="pageicon {literal}{sorter: false}{/literal}">&nbsp;</th>
   </tr>
  </thead>
  {foreach from=$promotions item='entry' name='promos'}
    <tr>
      <td>{$entry.id}</td>
      <td><a href="{$entry.edit_url}" title="{$mod->Lang('edit')}">{$entry.name}</a></td>
      <td>
        {* start date *}
        {if $entry.start_date_ut > $smarty.now}
          <span style="color: yellow;">{$entry.start_date|cms_date_format}</span>
        {else}
          {$entry.start_date|cms_date_format}
        {/if}
      </td>
      <td>
        {* end date *}
        {if $entry.end_date_ut < $smarty.now}
          <span style="color: red;">{$entry.end_date|cms_date_format}</span>
        {else}
          {$entry.end_date|cms_date_format}
        {/if}
      </td>
      <td>{if $entry.item_order > 1}{mod_action_link module='EcPromotions' action='admin_movepromo' dir='up' promo=$entry.id image='icons/system/arrow-u.gif' imageonly=1}{/if}</td>
      <td>{if !$smarty.foreach.promos.last}{mod_action_link module='EcPromotions' action='admin_movepromo' dir='down' promo=$entry.id image='icons/system/arrow-d.gif' imageonly=1}{/if}</td>
      <td>{$entry.edit_link}</td>
      <td>{$entry.delete_link}</td>
    </tr>
  {/foreach}
  <tbody>
  </tbody>
</table>
{/if}

<div class="pageoptions"><p class="pageoptions">{$addlink}</p></div>
