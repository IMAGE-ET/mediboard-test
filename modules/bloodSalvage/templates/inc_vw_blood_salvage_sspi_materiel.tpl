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
  <select name="cell_saver_id" onchange="submitFormAjax(this.form, 'systemMsg');">
    <option value="null">&mdash; Cell Saver</option>
		{{foreach from=$list_cell_saver key=id item=cell_saver}}
		<option value="{{$id}}" {{if $id == $blood_salvage->cell_saver_id}}selected="selected"{{/if}}>{{$cell_saver->_view}}</option> 
		{{/foreach}}
	</select>
	</form>
	</td>
	<th style="width:10%">
    <b>{{mb_label object=$blood_salvage field=receive_kit}}</b>
  </th>
  <td>
    <form name="recueil" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
	    <input type="hidden" name="m" value="bloodSalvage" />
	    <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
	    <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
	    <input type="hidden" name="del" value="0" />
      {{mb_field object=$blood_salvage field=receive_kit style="text-transform:uppercase;"}}
	    <button class="tick notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg');"></button>
	    <button class="cancel notext" type="button" onclick="this.form.receive_kit.value='' ;submitFormAjax(this.form, 'systemMsg');"></button>
    </form>
  </td>
</tr>
</table>