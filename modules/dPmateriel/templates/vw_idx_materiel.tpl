<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <a class="button" href="index.php?m=dPmateriel&amp;tab=vw_idx_materiel&amp;materiel_id=0">
        Créer une nouvelle fiche
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Nom</th>
          <th>Catégorie</th>
          <th>Description</th>
          <th>Code Barre</th>
        </tr>
        {foreach from=$listMateriel item=curr_materiel}
        <tr>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_materiel&amp;materiel_id={$curr_materiel->materiel_id}" title="Modifier le matériel">
              {$curr_materiel->materiel_id}
            </a>
          </td>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_materiel&amp;materiel_id={$curr_materiel->materiel_id}" title="Modifier le matériel">
              {$curr_materiel->nom}
            </a>
          </td>
          <td>{$curr_materiel->_ref_category->category_name}</td>
          <td>{$curr_materiel->description|nl2br}</td>
          <td>{$curr_materiel->code_barre}</td>
        </tr>
        {/foreach}        
      </table>
    </td>
    <td class="halfPane">
      <form name="editMat" action="./index.php?m={$m}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_materiel_aed" />
	  <input type="hidden" name="materiel_id" value="{$materiel->materiel_id}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {if $materiel->materiel_id}
          <th class="title" colspan="2" style="color:#f00;">Modification de la fiche {$materiel->_view}</th>
          {else}
          <th class="title" colspan="2">Création d'une fiche</th>
          {/if}
        </tr>   
        <tr>
          <th><label for="nom" title="Nom du matériel, obligatoire">Nom</label></th>
          <td><input name="nom" title="{$materiel->_props.nom}" type="text" value="{$materiel->nom}" /></td>
        </tr>
        <tr>
          <th><label for="category_id" title="Catégorie du matériel, obligatoire">Catégorie</label></th>
          <td><select name="category_id" title="{$materiel->_props.category_id}">
            <option value="">&mdash; Choisir une catégorie</option>
            {foreach from=$listCategories item=curr_category}
              <option value="{$curr_category->category_id}" {if $materiel->category_id == $curr_category->category_id} selected="selected" {/if} >
              {$curr_category->category_name}
              </option>
            {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="code_barre" title="Code Barre du matériel, numérique">Code Barre</label></th>
          <td><input name="code_barre" title="{$materiel->_props.code_barre}" type="text" value="{$materiel->code_barre}" /></td>
        </tr>
        <tr>
          <th><label for="description" title="Description du matériel">Description</label></th>
          <td><textarea title="{$materiel->_props.description}" name="description">{$materiel->description}</textarea></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="submit">Valider</button>
            {if $materiel->materiel_id}
              <button type="button" onclick="confirmDeletion(this.form,{ldelim}typeName:'le matériel',objName:'{$materiel->_view|escape:javascript}'{rdelim})">Supprimer</button>
            {/if}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
         {if $materiel->materiel_id}
         <a class="button" href="index.php?m=dPmateriel&amp;tab=vw_idx_stock&amp;stock_id=0&amp;materiel_id={$materiel->materiel_id}">
           Créer un nouveau stock pour ce matériel
         </a>
         {/if}
         <table class="tbl">
         <tr>
           <th class="title" colspan="3">Stock(s) correspondant(s)</th>
         </tr>
         <tr>
           <th>Groupe</th>
           <th>Seuil de Commande</th>
           <th>Quantité</th>
         </tr>
         {foreach from=$materiel->_ref_stock item=curr_stock}
         <tr>
           <td>{$curr_stock->_ref_group->text}</td>
           <td>{$curr_stock->seuil_cmd}</td>
           <td>{$curr_stock->quantite}</td>
         </tr>
         {foreachelse}
         <tr>
           <td class="button" colspan="3">Aucun stock trouvé</td>
         </tr>
         {/foreach}
       </table>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Référence(s) correspondante(s)</th>
        </tr>
        <tr>
           <th>Fournisseur</th>
           <th>Quantité</th>
           <th>Prix</th>
           <th>Prix Unitaire</th>
         </tr>
         {foreach from=$materiel->_ref_refMateriel item=curr_refMateriel}
         <tr>
           <td>{$curr_refMateriel->_ref_fournisseur->societe}</td>
           <td>{$curr_refMateriel->quantite}</td>
           <td>{$curr_refMateriel->prix}</td>
           <td>{$curr_refMateriel->_prix_unitaire|string_format:"%.2f"}</td>
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