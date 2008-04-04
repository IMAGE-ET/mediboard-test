{{mb_include_script module="dPpatients" script="autocomplete"}}

<script type="text/javascript">
function pageMain() {
  initInseeFields("edit_societe", "postal_code", "city");
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="2">
      <a class="buttonnew" href="?m=dPstock&amp;tab=vw_idx_societe&amp;societe_id=0">
        Nouvelle société
      </a>
      <table class="tbl">
        <tr>
          <th>Société</th>
          <th>Correspondant</th>
          <th>Adresse</th>
          <th>Téléphone</th>
          <th>E-Mail</th>
        </tr>
        {{foreach from=$list_societes item=curr_societe}}
        <tr {{if $curr_societe->_id == $societe->_id}}class="selected"{{/if}}>
          <td class="text">
            <a href="?m=dPstock&amp;tab=vw_idx_societe&amp;societe_id={{$curr_societe->_id}}" title="Modifier la société">
              {{$curr_societe->_view}}
            </a>
          </td>
          <td class="text">{{mb_value object=$curr_societe field=contact_name}} {{mb_value object=$curr_societe field=contact_surname}}</td>
          <td class="text">
            {{$curr_societe->address|nl2br}}<br />{{mb_value object=$curr_societe field=postal_code}} {{mb_value object=$curr_societe field=city}}
          </td>
          <td>{{mb_value object=$curr_societe field=phone}}</td>
          <td><a href="mailto:{{$curr_societe->email}}">{{$curr_societe->email}}</a></td>
        </tr>
        {{/foreach}}       
        
      </table>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <form name="edit_societe" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_societe_aed" />
	  <input type="hidden" name="societe_id" value="{{$societe->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $societe->_id}}
          <th class="title modify" colspan="2">Modification du societe {{$societe->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une Societé</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="name"}}</th>
          <td>{{mb_field object=$societe field="name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="address"}}</th>
          <td>{{mb_field object=$societe field="address"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="postal_code"}}</th>
          <td>
      		{{mb_field object=$societe field="postal_code" size="31" maxlength="5"}}
      		<div style="display:none;" class="autocomplete" id="postal_code_auto_complete"></div>
    	  </td>
        </tr>
        <tr> 
          <th>{{mb_label object=$societe field="city"}}</th>
          <td>
      		{{mb_field object=$societe field="city" size="31"}}
      		<div style="display:none;" class="autocomplete" id="city_auto_complete"></div>
    	  </td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="phone"}}</th>
          <td>{{mb_field object=$societe field="phone"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="email"}}</th>
          <td>{{mb_field object=$societe field="email"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="contact_name"}}</th>
          <td>{{mb_field object=$societe field="contact_name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$societe field="contact_surname"}}</th>
          <td>{{mb_field object=$societe field="contact_surname"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $societe->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la societé',objName:'{{$societe->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr> 
      </table>
      </form>
      {{/if}}
  {{if $societe->_id}}
      <button class="new" type="button" onclick="window.location='?m=dPstock&amp;tab=vw_idx_reference&amp;reference_id=0&amp;societe_id={{$societe->_id}}'">
        Nouvelle référence
      </button>
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Fournit ces références</th>
        </tr>
        <tr>
           <th>Produit</th>
           <th>Quantité</th>
           <th>Prix</th>
           <th>Prix Unitaire</th>
         </tr>
         {{foreach from=$societe->_ref_product_references item=curr_reference}}
         <tr>
           <td><a href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id={{$curr_reference->_id}}" title="Voir ou modifier la référence">{{$curr_reference->_ref_product->_view}}</a></td>
           <td>{{mb_value object=$curr_reference field=quantity}}</td>
           <td>{{mb_value object=$curr_reference field=price}}</td>
           <td>{{mb_value object=$curr_reference field=_unit_price}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="4">Aucune référence trouvée</td>
         </tr>
         {{/foreach}}
       </table>
      <button class="new" type="button" onclick="window.location='?m=dPproduct&amp;tab=vw_idx_product&amp;product_id=0&amp;societe_id={{$societe->_id}}'">
        Nouveau produit
      </button>
      <table class="tbl">
        <tr>
          <th class="title" colspan="3">Fabrique ces produits</th>
        </tr>
        <tr>
           <th>Nom</th>
           <th>Description</th>
           <th>Code barre</th>
         </tr>
         {{foreach from=$societe->_ref_products item=curr_product}}
         <tr>
           <td><a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$curr_product->_id}}" title="Voir ou modifier le produit">{{$curr_product->_view}}</a></td>
           <td>{{mb_value object=$curr_product field=description}}</td>
           <td>{{mb_value object=$curr_product field=code}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="3">Aucun produit trouvé</td>
         </tr>
         {{/foreach}}
       </table>
    </td>
  </tr>
  {{/if}}
</table>