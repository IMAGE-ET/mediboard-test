{literal}
<script language="JavaScript" type="text/javascript">

function nouveau() {
  var url = new Url;
  url.setModuleTab("dPcompteRendu", "addedit_modeles");
  url.addParam("compte_rendu_id", "0");
  url.redirect();
}

function supprimer() {
  var form = document.editFrm;
  form.del.value = 1;
  form.submit();
}

function checkModele() {
  var form = document.editFrm;
  var field = null;
   
  var fieldChir = form.elements['chir_id'];
  var fieldFunc = form.elements['function_id'];
  
  if (fieldChir && fieldFunc) {
    if (fieldChir.value == 0 && fieldFunc.value == 0) {
      alert("Le modèle doit être associé à une fonction ou un praticien");
      fieldChir.focus();
      return false;
    }
  }

  return checkForm(form);
}

{/literal}
</script>

<form name="editFrm" action="?m={$m}" method="post" onsubmit="return checkModele()">

<input type="hidden" name="m" value="{$m}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_modele_aed" />
<input type="hidden" name="compte_rendu_id" value="{$compte_rendu->compte_rendu_id}" />

<table class="main">

<tr>
  <td>
  
<table class="form">
  <tr>
    <th class="category" colspan="2">
      {if $compte_rendu->compte_rendu_id}
      <a style="float:right;" href="javascript:view_log('CCompteRendu',{$compte_rendu->compte_rendu_id})">
        <img src="images/history.gif" alt="historique" />
      </a>
      {/if}
      Informations sur le modèle
    </th>
  </tr>
  
  <tr>
    <th><label for="nom" title="Intitulé du modèle. Obligatoire">Nom:</label></th>
    <td><input type="text" name="nom" value="{$compte_rendu->nom}" title="{$compte_rendu->_props.nom}" /></td>
  </tr>
  
  <tr>
    <th><label for="function_id" title="Fonction à laquelle le modèle est associé">Fonction:</label></th>
    <td>
      <select name="function_id" onchange="this.form.chir_id.value = 0">
        <option value="0">&mdash; Associer à une fonction &mdash;</option>
        {foreach from=$listFunc item=curr_func}
          <option value="{$curr_func->function_id}" {if $curr_func->function_id == $compte_rendu->function_id} selected="selected" {/if}>
            {$curr_func->_view}
          </option>
        {/foreach}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="chir_id" title="Praticien auquel le modèle est associé">Praticien:</label></th>
    <td>
      <select name="chir_id" onchange="this.form.function_id.value = 0">
        <option value="0">&mdash; Associer à un praticien &mdash;</option>
        {foreach from=$listPrat item=curr_prat}
          <option value="{$curr_prat->user_id}" {if $curr_prat->user_id == $prat_id} selected="selected" {/if}>
            {$curr_prat->_view}
          </option>
        {/foreach}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="type" title="Contexte dans lequel est utilisé le modèle">Type de modèle: </label></th>
    <td>
      <select name="type">
        {foreach from=$ECompteRenduType item=curr_type}
          <option value="{$curr_type}" {if $curr_type == $compte_rendu->type} selected="selected" {/if}>
            {$curr_type}
          </option>
        {/foreach}
      </select>
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="2">
    {if $compte_rendu->compte_rendu_id}
      <input type="submit" value="Modifier" />
      <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'le modèle',objName:'{$compte_rendu->nom|escape:javascript}'{rdelim})" />
      <input type="button" value="Nouveau" onclick="nouveau()" />
    {else}
      <input type="submit" value="Créer" />
    {/if}
    </td>
  </tr>
</table>

  </td>
  <td class="greedyPane" style="height: 500px">
  {if $compte_rendu->compte_rendu_id}
    <textarea id="htmlarea" name="source">
    {$compte_rendu->source}
    </textarea>
  {/if}
  </td>
</tr>

</table>

</form>