<table class="tbl">
  <tr>
    <th>{{tr}}SSPI.Salle{{/tr}}</th>
    <th>{{tr}}SSPI.Praticien{{/tr}}</th>
    <th>{{tr}}SSPI.Patient{{/tr}}</th>
    <th>{{mb_title class=COperation field=entree_salle}}</th>
		<th>{{mb_title class=COperation field=debut_op}}</th>
  </tr>    
  {{foreach from=$listOperations item=curr_op}}
  <tr>
    <td>{{$curr_op->_ref_salle->nom}}</td>
    <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
    <td class="text">
    	<a href="?m={{$m}}&amp;tab=vw_soins_reveil&amp;operation_id={{$curr_op->_id}}" title="Soins">
    		{{$curr_op->_ref_sejour->_ref_patient->_view}}
			</a>
		</td>
    <td>{{mb_value object=$curr_op field=entree_salle}}</td>
		<td>
			{{if $curr_op->debut_op}}
		    {{mb_value object=$curr_op field=debut_op}}
			{{else}}
		    -
			{{/if}}	
	  </td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="20">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>

<script type="text/javascript">
  $('liencours').innerHTML = {{$listOperations|@count}};
</script>