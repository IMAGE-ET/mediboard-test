<!-- $Id$ -->

<script type="text/javascript">
printFicheAnesth = function(consultation_id, operation_id) {
  var url = new Url("dPcabinet", "print_fiche"); 
  url.addParam("consultation_id", consultation_id);
  url.addParam("operation_id", operation_id);
  url.popup(700, 500, "printFicheAnesth");
}

chooseAnesthCallback = function() {
  loadSejour({{$sejour->_id}}); 
}

printFicheBloc = function(operation_id) {
  var url = new Url("dPsalleOp", "print_feuille_bloc"); 
  url.addParam("operation_id", operation_id);
  url.popup(700, 500, "printFicheBloc");
}

refreshListIntervs = function() {
  var url = new Url("dPplanningOp", "ajax_vw_operations_sejour"); 
  url.addParam("sejour_id", {{$sejour->_id}});
  url.requestUpdate("intervs-sejour-{{$sejour->_guid}}");
}
</script>

{{if $sejour->_canRead}}
	<table class="tbl">
	  <tr>
	    <th class="title" colspan="4">
	      {{if $sejour->_ref_consult_anesth->_id && !$sejour->_ref_consult_anesth->operation_id}}
	        <button style="float: right" class="print" type="button" onclick="printFicheAnesth('{{$sejour->_ref_consult_anesth->_ref_consultation->_id}}');">
            Fiche d'anesthésie
          </button>
	      {{/if}}
	      {{tr}}CSejour-back-operations{{/tr}}
	    </th>
	  </tr>
	  <tr>
	    <th>{{tr}}COperation-chir_id{{/tr}}</th>
	    <th>{{tr}}Date{{/tr}}</th>
	    <th>{{tr}}COperation-_ext_codes_ccam{{/tr}}</th>
	    <th></th>
	  </tr>
	  <tbody id="intervs-sejour-{{$sejour->_guid}}">
	    {{mb_include module=dPplanningOp template=inc_info_list_operations}}
    </tbody>
	</table>
{{elseif $sejour->_id}}
  <div class="small-info">Vous n'avez pas accès au détail des interventions.</div>
{{/if}}