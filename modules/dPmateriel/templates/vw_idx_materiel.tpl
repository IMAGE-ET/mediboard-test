<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <a class="button new" href="?m=dPmateriel&amp;tab=vw_idx_materiel&amp;materiel_id=0">
        Créer une nouvelle fiche
      </a>
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>Catégorie</th>
          <th>Description</th>
          <th>Code Barre</th>
        </tr>
        {{foreach from=$listMateriel item=curr_materiel}}
        <tr {{if $curr_materiel->_id == $materiel->_id}}class="selected"{{/if}}>
          <td class="text">
            <a href="?m=dPmateriel&amp;tab=vw_idx_materiel&amp;materiel_id={{$curr_materiel->_id}}" title="Modifier le matériel">
              {{$curr_materiel->nom}}
            </a>
          </td>
          <td class="text">{{$curr_materiel->_ref_category->category_name}}</td>
          <td class="text">{{$curr_materiel->description|nl2br}}</td>
          <td>{{$curr_materiel->code_barre}}</td>
        </tr>
        {{/foreach}}        
      </table>
    </td>
    <td class="halfPane">
      <form name="editMat" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_materiel_aed" />
	  <input type="hidden" name="materiel_id" value="{{$materiel->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $materiel->_id}}
          <th class="title modify" colspan="2">Modification de la fiche {{$materiel->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une fiche</th>
          {{/if}}
        </tr>   
        <tr>
          <th>{{mb_label object=$materiel field="nom"}}</th>
          <td>{{mb_field object=$materiel field="nom"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$materiel field="category_id"}}</th>
          <td><select name="category_id" class="{{$materiel->_props.category_id}}">
            <option value="">&mdash; Choisir une catégorie</option>
            {{foreach from=$listCategories item=curr_category}}
              <option value="{{$curr_category->category_id}}" {{if $materiel->category_id == $curr_category->category_id}} selected="selected" {{/if}} >
              {{$curr_category->category_name}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$materiel field="code_barre"}}</th>
          <td>{{mb_field object=$materiel field="code_barre"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$materiel field="description"}}</th>
          <td>{{mb_field object=$materiel field="description"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $materiel->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le matériel',objName:'{{$materiel->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
         {{if $materiel->_id}}
         <button class="new" type="button" onclick="window.location='?m=dPmateriel&amp;tab=vw_idx_stock&amp;stock_id=0&amp;materiel_id={{$materiel->_id}}'">
           Créer un nouveau stock pour ce matériel
         </button>
         {{/if}}
         <table class="tbl">
         <tr>
           <th class="title" colspan="3">Stock(s) correspondant(s)</th>
         </tr>
         <tr>
           <th>Groupe</th>
           <th>Seuil de Commande</th>
           <th>Quantité</th>
         </tr>
         {{foreach from=$materiel->_ref_stock item=curr_stock}}
         <tr>
           <td class="text">{{$curr_stock->_ref_group->text}}</td>
           <td>{{$curr_stock->seuil_cmd}}</td>
           <td>{{$curr_stock->quantite}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="3">Aucun stock trouvé</td>
         </tr>
         {{/foreach}}
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
         {{foreach from=$materiel->_ref_refMateriel item=curr_refMateriel}}
         <tr>
           <td class="text">{{$curr_refMateriel->_ref_fournisseur->societe}}</td>
           <td>{{$curr_refMateriel->quantite}}</td>
           <td>{{$curr_refMateriel->prix|string_format:"%.2f"}}</td>
           <td>{{$curr_refMateriel->_prix_unitaire|string_format:"%.2f"}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="4">Aucune référence trouvée</td>
         </tr>
         {{/foreach}}
       </table>
    
    </td>
  </tr>
</table>