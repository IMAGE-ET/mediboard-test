<script type="text/javascript">
function submitForm(oForm) {
  submitFormAjax(oForm, 'systemMsg');
}
</script>

<table class="form">
<tr>
  <th class="category" colspan="4" >Cell saver</th>
</tr>
<tr>
  <th style="width:10%"><b>Cell Saver</b></th>
	<td>
	<form name="cell-saver-id{{$blood_salvage->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="bloodSalvage" />
  <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
  <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
  <input type="hidden" name="del" value="0" />
  <select name="cell_saver_id" onchange="submitForm(this.form)">
    <option value="null">&mdash; Cell Saver</option>
		{{foreach from=$list_cell_saver key=id item=name}}
		<option value="{{$id}}" {{if $id == $blood_salvage->cell_saver_id}}selected="selected"{{/if}}>{{$name}}</option> 
		{{/foreach}}
	</select>
	</form>
	</td>
	<td>
	</td>
</tr>
</table>