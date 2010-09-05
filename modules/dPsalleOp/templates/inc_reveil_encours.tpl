<table class="tbl">
  <tr>
    <th>{{tr}}SSPI.Salle{{/tr}}</th>
    <th>{{tr}}SSPI.Praticien{{/tr}}</th>
    <th>{{tr}}SSPI.Patient{{/tr}}</th>
    <th>{{mb_title class=COperation field=entree_salle}}</th>
		<th>{{mb_title class=COperation field=debut_op}}</th>
  </tr>    
  {{foreach from=$listOperations item=_operation}}
  <tr>
    <td>{{$_operation->_ref_salle->_shortview}}</td>
    <td class="text">
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab=vw_soins_reveil&amp;operation_id={{$_operation->_id}}">
      <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
        {{$_operation->_ref_patient->_view}}
      </span>
      </a>
    </td>
    <td>{{mb_value object=$_operation field=entree_salle}}</td>
		<td>
			{{if $_operation->debut_op}}
		    {{mb_value object=$_operation field=debut_op}}
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