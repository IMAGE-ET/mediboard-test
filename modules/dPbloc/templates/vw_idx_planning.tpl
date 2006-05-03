<!-- $Id: vw_idx_materiel.tpl,v 1.6 2005/03/08 18:13:49 rhum1 Exp $ -->

<script language="javascript" type="text/javascript">
{literal} 
function pageMain() {
  {/literal}
  regRedirectFlatCal("{$date}", "index.php?m={$m}&tab={$tab}&date=");
  {literal}
}

{/literal} 
</script>

<div id="calendar-container"></div>

<table class="tbl">
  <tr>
  	<th>Liste des spécialités</th>
  </tr>
  {foreach from=$listSpec item=curr_spec}
  <tr>
    <td class="text" style="background: #{$curr_spec->color};">{$curr_spec->text}</td>
  </tr>
  {/foreach}
</table>
