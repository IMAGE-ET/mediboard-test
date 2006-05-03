<form name="editFrm" action="?m={$m}" method="post" style="height: 650px">

<input type="hidden" name="m" value="{$m}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="_dialog" value="edit_compte_rendu" />
<input type="hidden" name="special" value="1" />
<input type="hidden" name="cr_valide" value="0" />
<input type="hidden" name="operation_id" value="{$op->operation_id}" />

<table class="form">
  {if $lists|@count}
  <tr>
    {foreach from=$lists item=curr_list}
    <td>{$curr_list->nom}</td>
    {/foreach}
    <td class="button" rowspan="2">
      <button onclick="javascript:this.form.special.value = 2;"><img src="modules/{$m}/images/tick.png" /></button>
    </td>
  </tr>
  <tr>
    {foreach from=$lists item=curr_list}
    <td>
      <select name="_liste{$curr_list->liste_choix_id}">
        {foreach from=$curr_list->_valeurs item=curr_valeur}
        <option>{$curr_valeur}</option>
        {/foreach}
      </select>
    </td>
    {/foreach}
  </tr>
  {/if}
  <tr>
    <td colspan="10" style="height: 600px">
      <textarea style="width: 99%" id="htmlarea" name="compte_rendu" rows="40">
        {$templateManager->document}
      </textarea>
    </td>
  </tr>
</table>

</form>