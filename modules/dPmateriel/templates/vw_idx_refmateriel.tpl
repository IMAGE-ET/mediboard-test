<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button new" href="?m=dPmateriel&amp;tab=vw_idx_refmateriel&amp;reference_id=0">
        Créer une nouvelle référence
      </a>
      <table class="tbl">
        <tr>
          <th>Fournisseur</th>
          <th>Matériel</th>
          <th>Quantité</th>
          <th>Prix</th>
          <th>Prix Unitaire</th>
        </tr>
        {{foreach from=$listReference item=curr_reference}}
        <tr {{if $curr_reference->_id == $reference->_id}}class="selected"{{/if}}>
          <td class="text">
            <a href="?m=dPmateriel&amp;tab=vw_idx_refmateriel&amp;reference_id={{$curr_reference->_id}}" title="Modifier la référence">
              {{$curr_reference->_ref_fournisseur->societe}}
            </a>
          </td>
          <td class="text">{{$curr_reference->_ref_materiel->nom|nl2br}}</td>
          <td>{{$curr_reference->quantite}}</td>
          <td>{{$curr_reference->prix|string_format:"%.2f"}}</td>
          <td>{{$curr_reference->_prix_unitaire|string_format:"%.2f"}}</td>
        </tr>
        {{/foreach}}
      </table>    
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <form name="editreference" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_refmateriel_aed" />
	  <input type="hidden" name="reference_id" value="{{$reference->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $reference->_id}}
          <th class="title modify" colspan="2">Modification de la référence {{$reference->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une référence</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="fournisseur_id"}}</th>
          <td><select name="fournisseur_id" class="{{$reference->_props.fournisseur_id}}">
            <option value="">&mdash; Choisir un Fournisseur</option>
            {{foreach from=$listFournisseur item=curr_fournisseur}}
              <option value="{{$curr_fournisseur->fournisseur_id}}" {{if $reference->fournisseur_id == $curr_fournisseur->fournisseur_id}} selected="selected" {{/if}} >
              {{$curr_fournisseur->societe}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="materiel_id"}}</th>
          <td><select name="materiel_id" class="{{$reference->_props.materiel_id}}">
            <option value="">&mdash; Choisir un Matériel</option>
            {{foreach from=$listCategory item=curr_cat}}
               <optgroup label="{{$curr_cat->category_name}}">
               {{foreach from=$curr_cat->_ref_materiel item=curr_materiel}}
                 <option value="{{$curr_materiel->materiel_id}}" {{if $reference->materiel_id == $curr_materiel->materiel_id}} selected="selected" {{/if}} >
                 {{$curr_materiel->nom}}
                 </option>
               {{/foreach}}
               </optgroup>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="quantite"}}</th>
          <td>{{mb_field object=$reference field="quantite"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="prix"}}</th>
          <td>{{mb_field object=$reference field="prix"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $reference->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la référence',objName:'{{$reference->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>