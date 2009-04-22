<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button new" href="?m=dPmateriel&amp;tab=vw_idx_stock&amp;stock_id=0">
        Créer un nouveau stock
      </a>
      <table class="tbl">
        <tr>
          <th>Matériel</th>
          <th>Groupe</th>
          <th>Seuil de Commande</th>
          <th>Quantité</th>
        </tr>
        {{foreach from=$listStock item=curr_stock}}
        <tr {{if $curr_stock->_id == $stock->_id}}class="selected"{{/if}}>
          <td class="text">
            <a href="?m=dPmateriel&amp;tab=vw_idx_stock&amp;stock_id={{$curr_stock->_id}}" title="Modifier le stock">
              {{$curr_stock->_ref_materiel->nom}} ({{$curr_stock->_ref_materiel->_ref_category->category_name}})
              {{if $curr_stock->_ref_materiel->code_barre}}<br />{{$curr_stock->_ref_materiel->code_barre}}{{/if}}
              {{if $curr_stock->_ref_materiel->description}}<br />{{$curr_stock->_ref_materiel->description|nl2br}}{{/if}}
            </a>
          </td>
          <td class="text">{{$curr_stock->_ref_group->_view}}</td>
          <td>{{$curr_stock->seuil_cmd}}</td>
          <td>
            {{mb_ternary var=msgClass test=$curr_stock->_rupture value=warning other=message}}
            <div class="{{$msgClass}}">
              {{$curr_stock->quantite}}
            </div>
          </td>
        </tr>
        {{/foreach}}
      </table>
        
    </td>
    <td class="halfPane">
      <form name="editStock" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_stock_aed" />  
	  <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
      <input type="hidden" name="del" value="0" />  
      <table class="form">
        <tr>
          {{if $stock->_id}}
          <th class="title modify" colspan="2">Modification du stock {{$stock->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un stock</th>
          {{/if}}
        </tr>  
        <tr>
          <th>{{mb_label object=$stock field="materiel_id"}}</th>
          <td><select name="materiel_id" class="{{$stock->_props.materiel_id}}">
            <option value="">&mdash; Choisir un Matériel</option>
            {{foreach from=$listCategory item=curr_cat}}
               <optgroup label="{{$curr_cat->category_name}}">
               {{foreach from=$curr_cat->_ref_materiel item=curr_materiel}}
                 <option value="{{$curr_materiel->materiel_id}}" {{if $stock->materiel_id == $curr_materiel->materiel_id}} selected="selected" {{/if}} >
                 {{$curr_materiel->nom}}
                 </option>
               {{/foreach}}
               </optgroup>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="group_id"}}</th>
          <td><select name="group_id" class="{{$stock->_props.group_id}}">
            <option value="">&mdash; Choisir un Groupe</option>
            {{foreach from=$listGroupes item=curr_groupes}}
              <option value="{{$curr_groupes->group_id}}" {{if $stock->group_id == $curr_groupes->group_id}} selected="selected" {{/if}} >
              {{$curr_groupes->text}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>        
        <tr>
          <th>{{mb_label object=$stock field="seuil_cmd"}}</th>
          <td>{{mb_field object=$stock field="seuil_cmd"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="quantite"}}</th>
          <td>{{mb_field object=$stock field="quantite"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $stock->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le stock',objName:'{{$stock->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
</table>