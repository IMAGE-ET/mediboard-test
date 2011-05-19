{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $now < $date_min || $now > $date_max}}
	<div class="small-info">
	  La date courante n'est pas comprise dans l'intervalle spécifié, les dispensations effectuées ne seront pas affichées.
	</div>
{{/if}}

<script type="text/javascript">
$$('a[href=#list-dispensations] small')[0].update('({{$dispensations|@count}})');

loadSuivi = function(sejour_id, user_id, cible, hide_obs) {
  var url = new Url("dPhospi", "httpreq_vw_dossier_suivi");
  url.addParam("sejour_id", sejour_id);
  url.addParam("user_id", user_id);
  url.addParam("cible", cible);
  if (hide_obs != null) {
    url.addParam("_hide_obs", hide_obs);
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
	  tbody.addClassName("done");
	  $('showDoneDispensations').checked ? tbody.show() : tbody.hide();
  } else {
	  tbody.show().removeClassName("done");
	}					 
}

toggleDoneDispensations = function(){
  $('showDoneDispensations').checked ? $$("tbody.done").invoke("show") : $$("tbody.done").invoke("hide");
}

{{if $mode_nominatif}}
  Main.add(function(){
   var options = {
      exactMinutes: false, 
      minInterval: 60
    };
  	
    var oForm = getForm("editBorneDisp");
    Calendar.regField(oForm.borne_min, null, options);
  	Calendar.regField(oForm.borne_max, null, options);
  });
{{/if}}

</script>

{{assign var=infinite value=$conf.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$conf.dPstock.CProductStockService.infinite_quantity}}

<label>
  <input type="checkbox" onclick="toggleDoneDispensations()" id="showDoneDispensations" /> Afficher les dispensations réalisées
</label>

<table class="tbl">
  {{if $mode_nominatif}}
	<tr>
		<td colspan="10">
			<form name="editBorneDisp" method="get" action="?">
				A partir du  {{$date_min|date_format:$conf.date}} à <input type="hidden" class="time" name="borne_min" value="{{$borne_min}}" />
				au {{$date_max|date_format:$conf.date}} à <input type="hidden" class="time" name="borne_max" value="{{$borne_max}}" />
				<button type="button" onclick="$V(getForm('filter').borne_min, $V(this.form.borne_min)); $V(getForm('filter').borne_max, $V(this.form.borne_max)); refreshLists();" class="search">
					{{tr}}Filter{{/tr}}
				</button>
			</form>
		</td>
	</tr>
  <tr>
    <th colspan="10" class="title">
      Dispensation pour {{$prescription->_ref_object->_ref_patient->_view}}
    </th>
  </tr>
  {{/if}}
  <tr>
    <th>Quantité<br />à administrer</th>
    <th>Quantité<br />à dispenser</th>
    {{if !$infinite}}
      <th>Stock<br />pharmacie</th>
    {{/if}}
    <th>Unité de<br />dispensation</th>
    <th style="width: 30%">
      <!-- <button style="float: right" type="button" onclick="dispenseAll('list-dispensations', refreshLists)" class="tick">Tout dispenser</button> -->
      Dispensation
    </th>
    <th>Déjà effectuées</th>
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