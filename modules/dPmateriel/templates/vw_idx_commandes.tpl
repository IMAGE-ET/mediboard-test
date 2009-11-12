<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button new" href="?m=dPmateriel&amp;tab=vw_idx_commandes&amp;commande_materiel_id=0">
        Créer une nouvelle commande
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="5">Commandes à recevoir</th>
        </tr>
        <tr>
          <th>Date</th>
          <th>Reference</th>
          <th>Quantité</th>
          <th>Prix</th>
        </tr>
        {{foreach from=$listCommandesARecevoir item=curr_commande}}
        <tr {{if $curr_commande->_id == $commande->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m=dPmateriel&amp;tab=vw_idx_commandes&amp;commande_materiel_id={{$curr_commande->_id}}" title="Modifier la commande">
              {{mb_value object=$curr_commande field="date"}}
            </a>
          </td>
          <td>{{$curr_commande->_ref_reference->_view}}</td>
          <td>{{mb_value object=$curr_commande field="quantite"}}</td>
          <td>{{mb_value object=$curr_commande field="prix"}}</td>
        </tr>
        {{/foreach}}
      </table>
      <table class="tbl">
        <tr>
          <th class="title" colspan="5">Commandes reçu</th>
        </tr>
        <tr>
          <th>Date</th>
          <th>Reference</th>
          <th>Quantité</th>
          <th>Prix</th>
        </tr>
        {{foreach from=$listCommandesRecu item=curr_commande}}
        <tr {{if $curr_commande->_id == $commande->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m=dPmateriel&amp;tab=vw_idx_commandes&amp;commande_materiel_id={{$curr_commande->_id}}" title="Modifier la commande">
              {{mb_value object=$curr_commande field="date"}}
            </a>
          </td>
          <td>{{$curr_commande->_ref_reference->_view}}</td>
          <td>{{mb_value object=$curr_commande field="quantite"}}</td>
          <td>{{mb_value object=$curr_commande field="prix"}}</td>
        </tr>
        {{/foreach}}
      </table>    
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <form name="editcommande" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_commande_aed" />
      <input type="hidden" name="commande_materiel_id" value="{{$commande->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $commande->_id}}
          <th class="title modify" colspan="2">Modification de la commande {{$commande->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une commande</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$commande field="date"}}</th>
          <td>{{mb_field object=$commande field="date" form="editcommande" register=true}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$commande field="reference_id"}}</th>
          <td>
            <select name="reference_id" class="{{$commande->_props.reference_id}}">
              {{foreach from=$listReferences item=curr_reference}}
                <option value="{{$curr_reference->_id}}" {{if $commande->reference_id == $curr_reference->reference_id}} selected="selected" {{/if}} >
                {{$curr_reference->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$commande field="quantite"}}</th>
          <td>{{mb_field object=$commande field="quantite"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$commande field="prix"}}</th>
          <td>{{mb_field object=$commande field="prix"}}</td>
        <tr>
        <tr>
          <th>{{mb_label object=$commande field="recu"}}</th>
          <td>{{mb_field object=$commande field="recu"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $commande->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la commande',objName:'{{$commande->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>