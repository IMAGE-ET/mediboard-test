<form name="cell-saver-infos{{$blood_salvage->_id}}" action="?m={{$m}}" method="post">

<input type="hidden" name="m" value="bloodSalvage" />
<input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
<input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
<input type="hidden" name="del" value="0" />

{{assign var=submit value=submitInfos}}


<table class="form">
  <tr>
	<th class="category" colspan="8"> Volumes</th>
	</tr>
	<tr>
	  <th>{{mb_label object=$blood_salvage field=wash_volume}}</th>
		<td class="button">
		  <input name="wash_volume" size="2" maxLength="4" type="text" value="{{$blood_salvage->wash_volume}}" />
		   ml
		  <button type="button" class="tick notext" onclick="{{$submit}}(this.form);">{{tr}}Save{{/tr}}</button>
			<button type="button" class="cancel notext" onclick="this.form.wash_volume.value = ''; {{$submit}}(this.form);">{{tr}}Cancel{{/tr}}</button>
		</td>
		<th>{{mb_label object=$blood_salvage field=hgb_pocket}}</th>
		<td class="button">
		  <input name="hgb_pocket" size="2" maxLength="4" type="text" value="{{$blood_salvage->hgb_pocket}}" /> 
		  g/dl
		  <button type="button" class="tick notext" onclick="{{$submit}}(this.form);">{{tr}}Save{{/tr}}</button>  
		  <button type="button" class="cancel notext" onclick="this.form.hgb_pocket.value = ''; {{$submit}}(this.form);">{{tr}}Cancel{{/tr}}</button>
		</td>
	</tr>
	<tr>
	  <th>{{mb_label object=$blood_salvage field=saved_volume}}</th>
		<td class="button">
		  <input name="saved_volume" size="2" maxLength="4" type="text" value="{{$blood_salvage->saved_volume}}" />
		   ml
		  <button type="button" class="tick notext" onclick="{{$submit}}(this.form);">{{tr}}Save{{/tr}}</button>	   
		  <button type="button" class="cancel notext" onclick="this.form.saved_volume.value = ''; {{$submit}}(this.form);">{{tr}}Cancel{{/tr}}</button>
		</td>
	  <th>{{mb_label object=$blood_salvage field=hgb_patient}}</th>	
		<td class="button">
		  <input name="hgb_patient" size="2" maxLength="4" type="text" value="{{$blood_salvage->hgb_patient}}" /> 
		  g/dl
		  <button type="button" class="tick notext" onclick="{{$submit}}(this.form);">{{tr}}Save{{/tr}}</button>  
		  <button type="button" class="cancel notext" onclick="this.form.hgb_patient.value = ''; {{$submit}}(this.form);">{{tr}}Cancel{{/tr}}</button>
		</td>
	</tr>
	<tr>
  <th>{{mb_label object=$blood_salvage field=transfused_volume}}</th>
	<td class="button">
      <input name="transfused_volume" size="2" maxLength="4" type="text" value="{{$blood_salvage->transfused_volume}}" /> 
      ml
      <button type="button" class="tick notext" onclick="{{$submit}}(this.form);">{{tr}}Save{{/tr}}</button>  
      <button type="button" class="cancel notext" onclick="this.form.transfused_volume.value = ''; {{$submit}}(this.form);">{{tr}}Cancel{{/tr}}</button>
	</td>
	<td colspan="2"></td>
	</tr>
</table>
</form>

