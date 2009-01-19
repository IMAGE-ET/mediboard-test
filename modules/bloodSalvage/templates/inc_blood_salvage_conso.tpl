<form name="anticoagulant{{$blood_salvage->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="bloodSalvage" />
  <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
  <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
  <input type="hidden" name="del" value="0" />

  <table class="form">
    <tr>
      <th class="category" colspan="6">Consommables</th>
    </tr>
    <tr>
      <th style="width:10%">Modèle</th>
      <td>
        <select name="cell_saver_id" onchange="submitFormAjax(this.form, 'systemMsg');">
          <option value="">&mdash; Cell Saver</option>
          {{foreach from=$list_cell_saver key=id item=cell_saver}}
          <option value="{{$id}}" {{if $id == $blood_salvage->cell_saver_id}}selected="selected"{{/if}}>{{$cell_saver->_view}}</option> 
          {{/foreach}}
        </select>
      </td>
      <th>{{mb_label object=$blood_salvage field=receive_kit_ref}}</th>
      <td>
        {{mb_field object=$blood_salvage field=receive_kit_ref style="text-transform:uppercase;" size=7}}
        <button class="tick notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg');"></button>
        <button type="button" class="cancel notext" onclick="this.form.receive_kit_ref.value='';this.form.receive_kit_lot.value='';submitFormAjax(this.form, 'systemMsg');">{{tr}}Cancel{{/tr}}</button>
      </td>
    </tr>
  	<tr>
  	  <th style="width:10%">{{mb_label object=$blood_salvage field=anticoagulant_cip}}</th>
      <td>
  			<select name="anticoagulant_cip" onchange="submitFormAjax(this.form, 'systemMsg');">
    		  <option value=""> &mdash; {{tr}}CBloodSalvage-anticoagulant_cip{{/tr}}</option>
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
  		</td>
  	  <th>{{mb_label object=$blood_salvage field=receive_kit_lot}}</th>
      <td>{{mb_field object=$blood_salvage field=receive_kit_lot style="text-transform:uppercase;" size=10}}</td>
  	</tr>
  </table>
</form>