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
  $$('a[href=#list-dispensations] small').first().update('({{$dispensations|@count}})');
  
  loadSuivi = function(sejour_id) {
   var urlSuivi = new Url;
   urlSuivi.setModuleAction("dPhospi", "httpreq_vw_dossier_suivi");
   urlSuivi.addParam("sejour_id", sejour_id);
   urlSuivi.requestUpdate("list-transmissions", { waitingText: null } );
  }
 
	submitSuivi = function(oForm) {
	  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { 
	    loadSuivi(oForm.sejour_id.value);
	  } });
	}

</script>

{{if $dPconfig.dPstock.CProductStockGroup.infinite_quantity == 1}}
  {{assign var=infinite value=1}}
{{else}}
  {{assign var=infinite value=0}}
{{/if}}

<table class="tbl">
  {{if $mode_nominatif}}
  <tr>
    <th colspan="10" class="title">
      Dispensation pour {{$prescription->_ref_object->_ref_patient->_view}}
    </th>
  </tr>
  {{/if}}
  <tr>
    <th>Quantité<br /> à administrer</th>
    <th>Quantité<br /> à dispenser</th>
    {{if !$infinite}}
      <th>Stock<br />pharmacie</th>
    {{/if}}
    <th style="width: 30%">
      <!-- <button style="float: right" type="button" onclick="dispenseAll()" class="tick">Tout dispenser</button> -->
      Dispensation
    </th>
    <th>Déjà effectuées</th>
    <th>Stock<br /> du service</th>
    <th>Unité de dispensation</th>
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