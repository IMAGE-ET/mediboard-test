{literal}
<script type="text/javascript">
function updateFields(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  $('editFournisseur_codepostal').value = dn[0].firstChild.firstChild.nodeValue;
  $('editFournisseur_ville').value = dn[2].firstChild.nodeValue;
}

function pageMain() {
  new Ajax.Autocompleter(
    'editFournisseur_codepostal',
    'codepostal_auto_complete',
    'index.php?m=dPmateriel&ajax=1&suppressHeaders=1&a=httpreq_do_insee_autocomplete', {
      minChars: 2,
      frequency: 0.15,
      updateElement: updateFields
    }
  );
  new Ajax.Autocompleter(
    'editFournisseur_ville',
    'ville_auto_complete',
    'index.php?m=dPmateriel&ajax=1&suppressHeaders=1&a=httpreq_do_insee_autocomplete', {
      minChars: 4,
      frequency: 0.15,
      updateElement: updateFields
    }
  );
}

</script>
{/literal}

<table class="main">
  <tr>
    <td class="halfPane" rowspan="2">
      <a class="buttonnew" href="index.php?m=dPmateriel&amp;tab=vw_idx_fournisseur&amp;fournisseur_id=0">
        Ajouter un nouveau fournisseur
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Société</th>
          <th>Correspondant</th>
          <th>Adresse</th>
          <th>Téléphone</th>
          <th>E-Mail</th>
        </tr>
        {foreach from=$listFournisseur item=curr_fournisseur}
        <tr>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_fournisseur&amp;fournisseur_id={$curr_fournisseur->fournisseur_id}" title="Modifier le fournisseur">
              {$curr_fournisseur->fournisseur_id}
            </a>
          </td>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_fournisseur&amp;fournisseur_id={$curr_fournisseur->fournisseur_id}" title="Modifier le fournisseur">
              {$curr_fournisseur->societe}
            </a>
          </td>
          <td>{$curr_fournisseur->nom} {$curr_fournisseur->prenom}</td>
          <td>
            {$curr_fournisseur->adresse|nl2br}<br />{$curr_fournisseur->codepostal} {$curr_fournisseur->ville}
          </td>
          <td>{$curr_fournisseur->telephone}</td>
          <td>{$curr_fournisseur->mail}</td>
        </tr>
        {/foreach}       
        
      </table>
    </td>
    <td class="halfPane">
      {if $canEdit}
      <form name="editFournisseur" action="./index.php?m={$m}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_fournisseur_aed" />
	  <input type="hidden" name="fournisseur_id" value="{$fournisseur->fournisseur_id}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {if $fournisseur->fournisseur_id}
          <th class="title" colspan="2" style="color:#f00;">Modification du fournisseur {$fournisseur->_view}</th>
          {else}
          <th class="title" colspan="2">Création d'un fournisseur</th>
          {/if}
        </tr>
        <tr>
          <th><label for="societe" title="Société, obligatoire">Société</label></th>
          <td><input name="societe" title="{$fournisseur->_props.societe}" type="text" value="{$fournisseur->societe}" /></td>
        </tr>
        <tr>
          <th><label for="adresse" title="Adresse de la société">Adresse</label></th>
          <td><textarea title="{$fournisseur->_props.adresse}" name="adresse">{$fournisseur->adresse}</textarea></td>
        </tr>
        <tr>
          <th><label for="codepostal" title="Code Postal">Code Postal</label></th>
          <td>
            <input size="31" maxlength="5" type="text" name="codepostal" title="{$fournisseur->_props.codepostal}" value="{$fournisseur->codepostal}" />
            <div style="display:none;" class="autocomplete" id="codepostal_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th><label for="ville" title="Ville">Ville</label></th>
          <td>
           
            <input size="31" type="text" name="ville" value="{$fournisseur->ville}" title="{$fournisseur->_props.ville}" />
          <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th><label for="telephone" title="Téléphone">Téléphone</label></th>
          <td><input name="telephone" title="{$fournisseur->_props.telephone}" type="text" value="{$fournisseur->telephone}" /></td>
        </tr>
        <tr>
          <th><label for="mail" title="E-Mail valide">E-Mail</label></th>
          <td><input name="mail" title="{$fournisseur->_props.mail}" type="text" value="{$fournisseur->mail}" /></td>
        </tr>
        <tr>
          <th><label for="nom" title="Nom du contact">Nom du contact</label></th>
          <td><input name="nom" title="{$fournisseur->_props.nom}" type="text" value="{$fournisseur->nom}" /></td>
        </tr>
        <tr>
          <th><label for="prenom" title="Prénom du contact">Prénom du Contact</label></th>
          <td><input name="prenom" title="{$fournisseur->_props.prenom}" type="text" value="{$fournisseur->prenom}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {if $fournisseur->fournisseur_id}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{ldelim}typeName:'le fournisseur',objName:'{$fournisseur->_view|escape:javascript}'{rdelim})">Supprimer</button>
            {/if}
          </td>
        </tr> 
      </table>
      </form>
      {/if}
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      {if $fournisseur->fournisseur_id}
      <button class="new" type="button" onclick="window.location='index.php?m=dPmateriel&amp;tab=vw_idx_refmateriel&amp;reference_id=0&amp;fournisseur_id={$fournisseur->fournisseur_id}'">
        Créer une nouvelle référence pour ce fournisseur
      </button>
      {/if}
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Référence(s) correspondante(s)</th>
        </tr>
        <tr>
           <th>Matériel</th>
           <th>Quantité</th>
           <th>Prix</th>
           <th>Prix Unitaire</th>
         </tr>
         {foreach from=$fournisseur->_ref_references item=curr_refmateriel}
         <tr>
           <td>{$curr_refmateriel->_ref_materiel->nom}</td>
           <td>{$curr_refmateriel->quantite}</td>
           <td>{$curr_refmateriel->prix}</td>
           <td>{$curr_refmateriel->_prix_unitaire|string_format:"%.2f"}</td>
         </tr>
         {foreachelse}
         <tr>
           <td class="button" colspan="4">Aucune référence trouvée</td>
         </tr>
         {/foreach}
       </table>
    </td>
  </tr>
</table>