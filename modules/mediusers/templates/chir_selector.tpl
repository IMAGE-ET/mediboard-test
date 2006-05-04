<!-- $Id$ -->

{literal}
<script type="text/javascript">
function setClose(){
  var list = document.frmSelector.list;
  var key = list.options[list.selectedIndex].value;
  var val = list.options[list.selectedIndex].text;
  window.opener.setChir(key,val);
  window.close();
}
</script>
{/literal}

<form action="index.php" target="_self" name="frmSelector" method="get">

<input type="hidden" name="m" value="mediusers" />
<input type="hidden" name="a" value="chir_selector" />
<input type="hidden" name="dialog" value="1" />

<table class="form">

<tr>
  <th class="category" colspan="2">Critères de tri</th>
</tr>

<tr>
  <th><label for="spe" title="Spécialité du chirurgien">Spécialité :</label></th>
  <td>
    <select name="spe" onChange="this.form.submit()">
      <option value="">&mdash; Trier par spécialité</option>
      {foreach from=$specs item=curr_spec}
      <option value="{$curr_spec->function_id}" {if $curr_spec->function_id == $spe} selected="selected"{/if}>
        {$curr_spec->text}
      </option>
      {/foreach}
    </select>
  </td>
</tr>

<tr>
  <th><label for="name" title="Nom partiel ou complet du chirurgien">Nom :</label></th>
  <td><input name="name" value="{$name}" size="30" /> <input type="submit" value="rechercher" /></td>
</tr>

<tr>
  <th class="category" colspan="2">Choix du praticien</th>
</tr>

<tr>
  <td colspan="2">
    <select name="list"  size="8">
      <option value="0" selected="selected">&mdash; Choisir un praticien</option>
      {foreach from=$prats item=curr_prat}
      <option value="{$curr_prat->user_id}" ondblclick="setClose()">{$curr_prat->_view}</option>
      {/foreach}
    </select>
  </td>
</tr>

<tr>
  <td class="button" colspan="2">
    <input type="button" class="button" value="annuler" onclick="window.close()" />
    <input type="button" class="button" value="selectionner" onclick="setClose()" />
  </td>
</tr>

</table>

</form>
