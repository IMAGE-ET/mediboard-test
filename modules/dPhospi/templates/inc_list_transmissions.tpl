<table class="tbl">
  
  <tr>
    <th>{{tr}}Type{{/tr}}</th>
    <th>{{tr}}User{{/tr}}</th>
    <th>{{tr}}Date{{/tr}}</th>
    <th>{{tr}}Hour{{/tr}}</th>
    
    <th colspan="3">{{mb_title class=CTransmissionMedicale field=text}}</th>
  </tr>
  
  {{foreach from=$sejour->_ref_suivi_medical item=_suivi}}
 	{{include file=inc_line_suivi.tpl _suivi=$_suivi show_patient=false}}
  {{/foreach}}
</table>


