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

loadSuivi = function(sejour_id, user_id) {
  var url = new Url("dPhospi", "httpreq_vw_dossier_suivi");
  url.addParam("sejour_id", sejour_id);
  url.addParam("user_id", user_id);
  url.requestUpdate("list-transmissions");
}

submitSuivi = function(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { 
    loadSuivi(oForm.sejour_id.value);
  } });
}

toggleDoneDispensations = function(){
  $$("tbody.done").invoke("toggle");
}
</script>

{{assign var=infinite value=$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$dPconfig.dPstock.CProductStockService.infinite_quantity}}

<label>
  <input type="checkbox" onclick="toggleDoneDispensations()" /> Afficher les dispensations réalisées
</label>

<table class="tbl">
  {{if $mode_nominatif}}
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
      <td colspan="10">{{tr}}CProductDelivery.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>