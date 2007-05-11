<script type="text/javascript">

function popObject() {
  var url = new Url;
  url.setModuleAction("system", "object_selector");
  url.addParam("selClass","CSejour");
  url.popup(600, 300, "Object Selector");
}

function pageMain() {
  regFieldCalendar("editfacture", "date");
}

function setObject(oObject){
	Console.debug(oObject);
}

</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <a class="buttonnew" href="index.php?m=dPfacturation&amp;tab=vw_idx_facture&amp;facture_id=0">
        Créer une nouvelle facture
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="5">Factures</th>
        </tr>
        <tr>
          <th>Date</th>
          <th>Nombre de lignes</th>
 		  <th>Montant</th>
        </tr>
        {{foreach from=$listFacture item=curr_facture}}
        <tr {{if $curr_facture->_id == $facture->_id}}class="selected"{{/if}}>
          <td>
           <a href="index.php?m=dPfacturation&amp;tab=vw_idx_facture&amp;facture_id={{$curr_facture->_id}}" title="Modifier la facture">
              {{mb_value object=$curr_facture field="date"}}
            </a>
          </td>
          <td>{{$curr_facture->_ref_items|@count}}</td>
           <td>{{mb_value object=$curr_facture field="_total"}}</td>
        </tr>
        {{/foreach}}
      </table>
  	</td>
  	<td class="halfPane">
      {{if $can->edit}}
      <form name="editfacture" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_facture_aed" />
      <input type="hidden" name="facture_id" value="{{$facture->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $facture->_id}}
          <th class="title modify" colspan="2">Modification de la facture {{$facture->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une facture</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$facture field="date"}}</th>
          <td class="date">{{mb_field object=$facture field="date" form="editfacture"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$facture field="sejour_id"}}</th>
          <td><button type="button" onclick="popObject()" class="search">Rechercher</button></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $facture->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la facture',objName:'{{$facture->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="halfPane">
         {{if $facture->_id}}
         <button class="new" type="button" onclick="window.location='index.php?m=dPfacturation&amp;tab=vw_idx_factureitem&amp;facture_item_id=0&amp;facture_id={{$facture->_id}}'">
           Créer un nouveau élément de la facture
         </button>
         {{/if}}
         <table class="tbl">
         <tr>
           <th class="title" colspan="4">Elements(s) correspondant(s)</th>
         </tr>
         <tr>
           <th>Element</th>
           <th>Prix H.T</th>
           <th>Taxe</th>
           <th>Prix T.T.C</th>
         </tr>
          {{foreach from=$facture->_ref_items item=curr_refFactureItem}}
         <tr>
           <td class="text">{{$curr_refFactureItem->libelle}}</td>
           <td>{{$curr_refFactureItem->prix_ht|string_format:"%.2f"}}</td>
           <td>{{$curr_refFactureItem->taxe}}</td>
           <td>{{$curr_refFactureItem->_ttc}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="4">Aucun élément trouvé</td>
         </tr>
         {{/foreach}}
         <tr>
         	<th colspan="3">TOTAL</th>
         	<td></td>
         </tr>       
       </table>
    </td>
  </tr>
 </table>