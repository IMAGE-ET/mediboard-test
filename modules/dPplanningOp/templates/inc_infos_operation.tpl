<!-- $Id$ -->

<script type="text/javascript">
printFicheAnesth = function(consultation_id, operation_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_fiche"); 
  url.addParam("consultation_id", consultation_id);
  url.addParam("operation_id", operation_id);
  url.popup(700, 500, "printFicheAnesth");
}

printFicheBloc = function(operation_id) {
    var url = new Url;
    url.setModuleAction("dPsalleOp", "print_feuille_bloc"); 
    url.addParam("operation_id", operation_id);
    url.popup(700, 500, "printFicheBloc");
  }
</script>

{{if $sejour->_canRead}}
	<table class="tbl">
	  <tr>
	    <th class="title" colspan="4">
	      {{if $sejour->_ref_consult_anesth->_id && !$sejour->_ref_consult_anesth->operation_id}}
	        <button style="float: right" class="print" type="button" onclick="printFicheAnesth('{{$sejour->_ref_consult_anesth->_ref_consultation->_id}}');">Fiche d'anesthésie</button>
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
	  {{foreach from=$sejour->_ref_operations item=_operation name=operation}}
	  <tr>
	    <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
	    </td>
	    <td>{{$_operation->_datetime|date_format:"%a %d %b %Y"}}</td>
	    {{if $_operation->annulee}}
	    <th class="category cancelled">
	      <strong>{{tr}}COperation-annulee{{/tr}}</strong>
			</th>
	    {{else}}
	    <td class="text">
	    	{{mb_include module=dPplanningOp template=inc_vw_operation}}
	    </td>
	    <td style="width: 1%;">
	      <button {{if $_operation->_ref_consult_anesth->_ref_consultation->_id}}class="print"{{else}}class="warning"{{/if}} style="width:11em;" type="button" onclick="printFicheAnesth('{{$_operation->_ref_consult_anesth->_ref_consultation->_id}}', '{{$_operation->_id}}');">Fiche d'anesthésie</button>
        <br />
        <button class="print" style="width:11em;" type="button" onclick="printFicheBloc('{{$_operation->_id}}');">Feuille de bloc</button>
	    </td>
	    {{/if}}
	  </tr>
	  {{foreachelse}}
	  <tr>
	    <td colspan="4"><em>{{tr}}COperation.none{{/tr}}</em></td>
	  </tr>
	  {{/foreach}}
	</table>
{{elseif $sejour->_id}}
  <div class="small-info">Vous n'avez pas accès au détail des interventions.</div>
{{/if}}