{literal}
<script type="text/javascript">
function checkGroup() {
  var form = document.group;
    
  if (form.text.value.length == 0) {
    alert("Intitulé manquant");
    form.text.focus();
    return false;
  }
    
  return true;
}
</script>
{/literal}

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="index.php?m={$m}&amp;tab={$tab}&amp;usergroup=0"><strong>Créer un groupe</strong></a>
      <table class="tbl">
        <tr><th>liste des groupes</th><th>Fonctions associées</th></tr>
        {foreach from=$listGroups item=curr_group}
        <tr>
          <td><a href="index.php?m={$m}&amp;tab={$tab}&amp;group_id={$curr_group->group_id}">{$curr_group->text}</a></td>
          <td><a href="index.php?m={$m}&amp;tab={$tab}&amp;group_id={$curr_group->group_id}">{$curr_group->_ref_functions|@count}</a></td>
        </tr>
        {/foreach}
      </table>
    </td>
    <td class="halfPane">
      <form name="group" action="./index.php?m={$m}" method="post" onsubmit="return checkGroup()">
      <input type="hidden" name="dosql" value="do_groups_aed" />
	  <input type="hidden" name="group_id" value="{$usergroup->group_id}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
          {if $usergroup->group_id}
            <a style="float:right;" href="javascript:view_log('CGroups',{$usergroup->group_id})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Modification du groupe &lsquo;{$usergroup->text}&rsquo;
          {else}
            Création d'un groupe
          {/if}
          </th>
        </tr>
        <tr>
          <th>
            <label for="text" title="intitulé du groupe, obligatoire.">Intitulé</label>
          </th>
          <td>
            <input type="text" title="{$usergroup->_props.text}" name="text" size="30" id="group_text" value="{$usergroup->text}" />
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          {if $usergroup->group_id}
            <input type="reset" value="Réinitialiser" />
            <input type="submit" value="Valider" />
            <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{rdelim}typeName:'le groupe',objName:'{$usergroup->text|escape:javascript}'{rdelim})" />
          {else}
            <input type="submit" name="btnFuseAction" value="Créer" />
          {/if}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>