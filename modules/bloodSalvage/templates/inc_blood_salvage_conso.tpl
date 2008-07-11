<table class="form">
  <tr>
    <th class="category" colspan="4">Consommables</th>
  </tr>
	<tr>
	  <td style="width:10%">
	    Anticoagulant
	  </td>
    <td>
	    <form name="consommables{{$blood_salvage->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
	    <input type="hidden" name="m" value="bloodSalvage" />
	    <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
	    <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
	    <input type="hidden" name="del" value="0" />
				<select name="anticoagulant_cip" onchange="submitFormAjax(this.form, 'systemMsg');">
	          <option value="null">&mdash; Anticoagulants</option>
					{{foreach from=$liste_anticoagulants item=anticoag}}
				    <option value="{{$anticoag->CodeCIP}}"{{if $anticoag->CodeCIP == $blood_salvage->anticoagulant_cip}}selected="selected"{{/if}}>{{$anticoag->Libelle}}</option>
					{{/foreach}}
				</select>
			</form>
		</td>
	  <td>
	  </td>
	</tr>
</table>
