<script type="text/javascript">

toggleTrans = function(trans_class){
	$('transmissions').select('tr').each(function(element){
	  trans_class ? (element.hasClassName(trans_class) ?  element.show() : element.hide()) : element.show();
	});
}

</script>
<table class="tbl">
  <tr>
    <th colspan="7" class="title">
			{{if !$without_del_form}}
				<div style="float: right">
			    <select style="width:150px" name="selCible" onchange="loadSuivi('{{$sejour->_id}}','',this.value)" >
			      <option value="">&mdash; Toutes les cibles</option>
			      {{foreach from=$cibles item=cibles_by_type}}
			        {{foreach from=$cibles_by_type item=_cible}}
			          <option {{if $_cible == $cible}} selected="selected" {{/if}} value="{{$_cible}}">{{$_cible|capitalize}}</option>
			        {{/foreach}}
			      {{/foreach}}
			    </select>
			    
			    {{if @$users}}
			    <select name="user_id" onchange="loadSuivi('{{$sejour->_id}}',this.value)">
			      <option value="">&mdash; Tous les utilisateurs</option>
			      {{foreach from=$users item=_user}}
			        <option value="{{$_user->_id}}" {{if $user_id == $_user->_id}} selected="selected"{{/if}}>{{$_user->_view}}</option>
			      {{/foreach}}
			    </select>
			    {{/if}}
		    </div>
	    {{/if}}
	    Observations et Transmissions 
    </th>
  </tr>
  <tr>
    <th>{{tr}}Type{{/tr}}</th>
    <th>{{tr}}User{{/tr}}</th>
    <th>{{tr}}Date{{/tr}}</th>
    <th>{{tr}}Hour{{/tr}}</th>
    <th>{{mb_title class=CTransmissionMedicale field=object_class}}</th>
    <th>{{mb_title class=CTransmissionMedicale field=text}}</th>
    <th />
  </tr>  
  <tbody {{if !$without_del_form}} id="transmissions"{{/if}}>
  {{foreach from=$list_transmissions item=_suivi}}
		{{if $_suivi instanceof CTransmissionMedicale || $_suivi instanceof CObservationMedicale}}
 	    {{mb_include module=dPhospi template=inc_line_suivi _suivi=$_suivi show_patient=false nodebug=true}}
		{{else}}
		   {{mb_include module=dPhospi template=inc_line_constante _constante=$_suivi nodebug=true}}
		{{/if}}
  {{foreachelse}}
  </tbody>
    <tr>
      <td colspan="7">{{tr}}CTransmissionMedicale.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>