<!--  $Id$ -->

{literal}
<script type="text/javascript">

function checkForm() {
  var form = document.editFrm;
  var field = null;
   
  if (field = form.elements['user_id']) {
    if (field.value == 0) {
      alert("Utilisateur indéterminé");
      field.focus();
      return false;
    }
  }

  if (field = form.elements['nom']) {    
    if (field.value == 0) {
      alert("Intitulé indéterminé");
      field.focus();
      return false;
    }
  }
    
  return true;
}
</script>
{/literal}

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{$m}" />
        
    <table class="form">

      <tr>
        <th><label for="filter_user_id" title="Filtrer les packs pour cet utilisateur">Utilisateur:</label></th>
        <td>
          <select name="filter_user_id" onchange="this.form.submit()">
            <option value="0">&mdash; Tous les utilisateurs</option>
            {foreach from=$users item=curr_user}
            <option value="{$curr_user->user_id}" {if $curr_user->user_id == $user_id} selected="selected" {/if}>
              {$curr_user->_view}
            </option>
            {/foreach}
          </select>
        </td>
      </tr>
    </table>

    </form>
    
    <table class="tbl">
    
    <tr>
      <th colspan="4"><strong>Packs créées</strong></th>
    </tr>
    
    <tr>
      <th>Utilisateur</th>
      <th>Nom</th>
      <th>modeles</th>
    </tr>

    {foreach from=$packs item=curr_pack}
    <tr>
      {eval var=$curr_pack->pack_id assign="pack_id"}
      {assign var="href" value="?m=$m&amp;tab=$tab&amp;pack_id=$pack_id"}
      <td><a href="{$href}">{$curr_pack->_ref_chir->_view}</a></td>
      <td><a href="{$href}">{$curr_pack->nom}</a></td>
      <td><a href="{$href}">{$curr_pack->_modeles|@count}</a></td>
    </tr>
    {/foreach}
      
    </table>

  </td>
  
  <td class="pane">

	<a href="index.php?m={$m}&amp;tab={$tab}&amp;pack_id=0"><strong>Créer un pack</strong></a>

    <form name="editFrm" action="?m={$m}" method="post" onsubmit="return checkForm()">

    <input type="hidden" name="dosql" value="do_pack_aed" />
    <input type="hidden" name="pack_id" value="{$pack->pack_id}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {if $pack->pack_id}
        Modification d'un pack
      {else}
        Création d'un pack
      {/if}
      </th>
    </tr>

    <tr>
      <th class="mandatory"><label for="user_id" title="Utilisateur concerné, obligatoire.">Utilisateur:</label></th>
      <td>
        <select name="chir_id">
          <option value="0">&mdash; Choisir un utilisateur</option>
          {foreach from=$users item=curr_user}
          <option value="{$curr_user->user_id}" {if $curr_user->user_id == $pack->chir_id} selected="selected" {/if}>
            {$curr_user->_view}
          </option>
          {/foreach}
        </select>
      </td>
    </tr>

    <tr>
      <th class="mandatory"><label for="name" title="intitulé du pack, obligatoire.">Intitulé:</label></th>
      <td><input type="text" name="nom" value="{$pack->nom}" /></td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {if $pack->pack_id}
        <input type="reset" value="Réinitialiser" />
        <input type="submit" value="Valider" />
        <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'le pack',objName:'{$pack->nom|escape:javascript}'{rdelim})" />
        {else}
        <input type="submit" value="Créer" />
        {/if}
      </td>
    </tr>

    </table>
    
    </form>

  </td>
  
  {if $pack->pack_id}
  <td class="pane">
  
    <table class="form">
      {if $pack->_modeles|@count}
      <tr><th class="category" colspan="2">Modèles du pack</th></tr>
      {foreach from=$pack->_modeles item=curr_modele}
      <tr><td>{$curr_modele->nom}</td>
        <td>
          <form name="delFrm{$pack->pack_id}" action="?m={$m}" method="post" onsubmit="return checkForm()">
          <input type="hidden" name="dosql" value="do_pack_aed" />
          <input type="hidden" name="pack_id" value="{$pack->pack_id}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="modeles" value="{$pack->modeles|escape:javascript}" />
          <input type="hidden" name="_del" value="{$curr_modele->compte_rendu_id}" />
          <button type="submit"><img src="modules/dPcompteRendu/images/trash.png" /></button>
          </form>
        </td>
      </tr>
      {/foreach}
      {/if}
      <tr><th class="category" colspan="2">Ajouter un modèle</th></tr>
      <tr><td colspan="2">
        <form name="addFrm" action="?m={$m}" method="post" onsubmit="return checkForm()">
        <input type="hidden" name="dosql" value="do_pack_aed" />
        <input type="hidden" name="pack_id" value="{$pack->pack_id}" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="modeles" value="{$pack->modeles|escape:javascript}" />
        <select name="_new">
          <option value="">&mdash; Choisir un modèle</option>
          <optgroup label="Modèles du praticien">
            {foreach from=$listModelePrat item=curr_modele}
            <option value="{$curr_modele->compte_rendu_id}">{$curr_modele->nom}</option>
            {/foreach}
          </optgroup>
          <optgroup label="Modèles du cabinet">
            {foreach from=$listModeleFunc item=curr_modele}
            <option value="{$curr_modele->compte_rendu_id}">{$curr_modele->nom}</option>
            {/foreach}
          </optgroup>
        </select>
        <button type="submit"><img src="modules/dPcompteRendu/images/check.png" /></button>
        </form>
      </td></tr>
    </table>
  
  </td>
  {/if}
  
</tr>

</table>
