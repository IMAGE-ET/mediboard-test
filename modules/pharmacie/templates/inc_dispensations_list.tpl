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
      <button type="button" onclick="dispenseAll()" class="tick">Tout dispenser</button>
    </th>
  </tr>
  {{/if}}
  <tr>
    <th>Quantité à administrer</th>
    <th>Quantité à dispenser</th>
    {{if !$infinite}}
      <th>Stock pharmacie</th>
    {{/if}}
    <th>Déjà effectuées</th>
    <th style="width: 30%">Dispensation</th>
    <th>Stock du service</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
  </tr>
  {{foreach from=$dispensations key=code_cip item=quantites}}

    <tbody id="dispensation_line_{{$code_cip}}" style="width: 100%">
    <!-- Affichage d'une ligne de dispensation -->
    {{include file="inc_dispensation_line.tpl" nodebug=true}}
    </tbody>
    
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductDelivery.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>