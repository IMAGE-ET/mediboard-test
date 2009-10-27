<!-- $Id$ -->

<script type="text/javascript">
printFiche = function(consultation_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_fiche"); 
  url.addParam("consultation_id", consultation_id);
  url.popup(700, 500, "printFiche");
}
</script>

{{if $sejour->_canRead}}
<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      {{if $sejour->_ref_consult_anesth->_id && !$sejour->_ref_consult_anesth->operation_id}}
        <button style="float: right" class="print" type="button" onclick="printFiche('{{$sejour->_ref_consult_anesth->_ref_consultation->_id}}');">Fiche</button>
      {{/if}}
      {{tr}}CSejour-msg-infoOper{{/tr}}
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
      <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$_operation->_id}}">
        {{$_operation->_ref_chir->_view}}
      </a>
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
      {{if $_operation->_ref_consult_anesth->_id}}
        <button class="print" type="button" onclick="printFiche('{{$_operation->_ref_consult_anesth->_ref_consultation->_id}}');">Fiche</button>
      {{/if}}
    </td>
    {{/if}}
  </tr>
  {{/foreach}}
</table>
 
{{else}}
<div class="small-info">Vous n'avez pas accès au détail des interventions.</div>
{{/if}}


