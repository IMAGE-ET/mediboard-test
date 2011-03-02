<script type="text/javascript">

submitActivite = function(oForm){
  return onSubmitFormAjax(oForm, { onComplete: function(){
    updateTasks('{{$sejour->_id}}');
    url.modaleObject.close(); 
  } } );
}

editTask = function(task_id){
  url = new Url("soins", "ajax_modal_task");
  url.addParam("task_id", task_id);
	url.addParam("sejour_id", "{{$sejour->_id}}");
	url.requestModal(600);
}

</script>

<div id="modal-task-{{$sejour->_id}}" style="display: none; width: 70%;">
</div>

<table class="tbl">	
  <tr>
    <th colspan="4" class="title">
    	<button type="button" class="add notext" onclick="editTask('0');" style="float: right;"></button>
    	Activités {{if $sejour->_ref_tasks}}({{$sejour->_ref_tasks|@count}}){{/if}}
		</th>
	</tr>		
	<tr>
    <th colspan="2">{{mb_title class="CSejourTask" field="description"}}</th>
		<th>{{mb_title class="CSejourTask" field="resultat"}}</th>
		<th></th>
	</tr>
	{{foreach from=$sejour->_ref_tasks item=_task}}
	  <tr>
	  	<td class="narrow"><input type="checkbox" disabled="disabled" {{if $_task->realise}}checked="checked"{{/if}} /></td>
	    <td {{if $_task->realise}}style="text-decoration: line-through"{{/if}}>{{mb_value object=$_task field="description"}}</td>
	    <td>{{mb_value object=$_task field="resultat"}}</td>
			<td class="narrow"><button type="button" class="edit notext" onclick="editTask('{{$_task->_id}}');"></button></td>
	  </tr>
	{{foreachelse}}
	  <tr>
	  	<td colspan="4" class="empty">
	  		{{tr}}CSejourTask.none{{/tr}}
	  	</td>
	  </tr>
	{{/foreach}}
</table>