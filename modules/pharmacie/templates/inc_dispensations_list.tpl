{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $now < $datetime_min || $now > $datetime_max}}
	<div class="small-info">
	  La date courante n'est pas comprise dans l'intervalle spécifié, les dispensations effectuées ne seront pas affichées.
	</div>
{{/if}}

<script type="text/javascript">
$$('a[href=#list-dispensations] small')[0].update('({{$dispensations|@count}})');

loadSuivi = function(sejour_id, user_id, cible, show_obs, show_trans) {
  var url = new Url("dPhospi", "httpreq_vw_dossier_suivi");
  url.addParam("sejour_id", sejour_id);
  url.addParam("user_id", user_id);
  url.addParam("cible", cible);
  if (!Object.isUndefined(show_obs)) {
    url.addParam("_show_obs", show_obs);
  }
  if (!Object.isUndefined(show_trans)) {
    url.addParam("_show_trans", show_trans);
  }
  url.requestUpdate("list-transmissions");
}

submitSuivi = function(oForm) {
  return onSubmitFormAjax(oForm, { onComplete: function() { 
    loadSuivi(oForm.sejour_id.value);
  } });
}

toggleLineDispensation = function(formName, done){
  var tbody = getForm(formName).up("tbody");
  if(done){
	  tbody.addClassName("done").setVisible($('showDoneDispensations').checked);
  } else {
	  tbody.removeClassName("done").show();
	}
}

toggleDoneDispensations = function(){
  $$("tbody.done").invoke("setVisible", $('showDoneDispensations').checked);
}

{{if $mode_nominatif}}
  Main.add(function(){
   var options = {
      exactMinutes: false, 
      minInterval: 60
    };
    var oForm = getForm("editBorneDisp");
  });
{{/if}}

</script>

{{assign var=infinite value=$conf.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$conf.dPstock.CProductStockService.infinite_quantity}}

<label>
  <input type="checkbox" onclick="toggleDoneDispensations()" id="showDoneDispensations" /> Afficher les dispensations réalisées
</label>

<table class="tbl">
  <tr>
    <th colspan="10" class="title">
      Dispensation pour 
			 {{if $mode_nominatif}}
			   {{$prescription->_ref_object->_ref_patient->_view}}
			 {{else}}
			   {{$service->_view}}
			 {{/if}}
    </th>
  </tr>
  <tr>
  	<th>Produit</th>
		
  	{{if $mode_nominatif}}
		  <th>Posologie</th>
		{{/if}}
		
    <th colspan="2">Qté à administrer</th>
    <!--<th colspan="2">Qté à dispenser</th>-->
		
    {{if !$infinite}}
      <th>Stock<br />pharmacie</th>
    {{/if}}
		
    <th style="width: 30%">
      <!-- <button style="float: right" type="button" onclick="dispenseAll('list-dispensations', refreshLists)" class="tick">Tout dispenser</button> -->
      Dispensation
    </th>
		
    <th>Effectuées</th>
		
    {{if !$infinite_service}}
      <th>Stock<br /> du service</th>
    {{/if}}
  </tr>
  {{foreach from=$dispensations key=code_cis item=quantites}}
    <tbody id="dispensation_line_{{$code_cis}}" style="width: 100%">
      <!-- Affichage d'une ligne de dispensation -->
      {{include file="inc_dispensation_line.tpl" nodebug=true}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CProductDelivery.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>