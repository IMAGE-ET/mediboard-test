<form name="cell-saver-id{{$blood_salvage->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="bloodSalvage" />
  <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
  <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
  <input type="hidden" name="del" value="0" />

  <table class="form">
    <tr>
      <th class="category" colspan="2">Cell saver</th>
    </tr>
    <tr>
    	<td>
        <select name="cell_saver_id" onchange="submitFormAjax(this.form, 'systemMsg');">
          <option value="">&mdash; Cell Saver</option>
      		{{foreach from=$list_cell_saver key=id item=cell_saver}}
      		<option value="{{$id}}" {{if $id == $blood_salvage->cell_saver_id}}selected="selected"{{/if}}>{{$cell_saver->_view}}</option> 
      		{{/foreach}}
      	</select>
    	</td>
      <td>
        {{mb_label object=$blood_salvage field=wash_kit_ref}}
        {{mb_field object=$blood_salvage field=wash_kit_ref style="text-transform:uppercase;" size=7}}
        
        {{mb_label object=$blood_salvage field=wash_kit_lot}}
        {{mb_field object=$blood_salvage field=wash_kit_lot style="text-transform:uppercase;" size=10}}
        
  	    <button class="tick notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg');"></button>
  	    <button class="cancel notext" type="button" onclick="this.form.wash_kit_ref.value=''; this.form.wash_kit_lot.value=''; submitFormAjax(this.form, 'systemMsg');"></button>
      </td>
    </tr>
  </table>
</form>