<table class="form">
  <tr>
    <th class="category" colspan="4">Consommables</th>
  </tr>
	<tr>
	  <th style="width:10%">
	    {{mb_label object=$blood_salvage field=anticoagulant_cip}}
	  </th>
    <td>
	    <form name="anticoagulant{{$blood_salvage->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
	    <input type="hidden" name="m" value="bloodSalvage" />
	    <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
	    <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
	    <input type="hidden" name="del" value="0" />
				<select name="anticoagulant_cip" onchange="submitFormAjax(this.form, 'systemMsg');">
			 {{foreach from=$anticoagulant_list key=key item=anticoag}}
			 {{if array_key_exists('dPmedicament', $modules)}}
					  {{if $inLivretTherapeutique}}
				    <option value="{{$anticoag->code_cip}}"{{if $anticoag->code_cip == $blood_salvage->anticoagulant_cip}}selected="selected"{{/if}}>{{$anticoag->_ref_produit->libelle}}</option>
					  {{/if}}
					  {{if !$inLivretTherapeutique}}
				    <option value="{{$anticoag->CodeCIP}}"{{if $anticoag->CodeCIP == $blood_salvage->anticoagulant_cip}}selected="selected"{{/if}}>{{$anticoag->Libelle}}</option>
					  {{/if}}
			 {{else}}
            <option value="{{$key}}"{{if $key == $blood_salvage->anticoagulant_cip}}selected="selected"{{/if}}>{{$anticoag}}</option>        
       {{/if}}
					{{/foreach}}
				</select>
			</form>
		</td>
	  <th style="width:10%">
	  {{mb_label object=$blood_salvage field=wash_kit}}
	  </th>
	  <td>
		  <form name="recueil" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
	      <input type="hidden" name="m" value="bloodSalvage" />
	      <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
	      <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
	      <input type="hidden" name="del" value="0" />
	      {{mb_field object=$blood_salvage field=wash_kit style="text-transform:uppercase;"}}
			  <button class="tick notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg');"></button>
        <button type="button" class="cancel notext" onclick="this.form.wash_kit.value='';submitFormAjax(this.form, 'systemMsg');">{{tr}}Cancel{{/tr}}</button>
		  </form>
	  </td>
	</tr>
</table>
