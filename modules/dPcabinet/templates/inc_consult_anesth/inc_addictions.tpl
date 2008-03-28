
<script type="text/javascript">
function submitAddiction(oForm){
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadDossiersMedicaux });
}

function finAddiction(oForm){
  oForm._hidden_addiction.value = oForm.addiction.value;
  oForm.addiction.value = "";
}

{{if $_is_anesth}}
  function copyAddiction(addiction_id){
   var oForm = document.frmCopyAddiction;
   oForm.addiction_id.value = addiction_id;
   submitFormAjax(oForm, 'systemMsg', { waitingText : null, onComplete : reloadDossierMedicalSejour });
  }
{{/if}}
</script>

<hr />

<form name="editAddictFrm" action="?m=dPcabinet" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_addiction_aed" />
  <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />

  {{if $current_m != "dPurgences"}}
  {{if $consult->_ref_consult_anesth->_id}}
  <!-- dossier_medical_id du sejour si c'est une consultation_anesth -->
  <input type="hidden" name="_sejour_id" value="{{$consult->_ref_consult_anesth->_ref_operation->_ref_sejour->_id}}" />
  {{/if}}
  {{/if}}


<table class="form">

  <tr>
    <!-- Auto-completion -->
    <th>{{mb_label object=$addiction field=_search}}</th>
    <td>
      {{mb_field object=$addiction field=_search}}
			{{mb_include_script module=dPcompteRendu script=aideSaisie}}
      <script type="text/javascript">
      	Main.add(function() {
	        new AideSaisie.AutoComplete("editAddictFrm" , "addiction", "type", "_search", "CAddiction", "{{$userSel->_id}}");
      	} );
      </script>
    </td>

    <td>
      {{mb_label object=$addiction field="addiction"}}
      {{foreach from=$addiction->_aides.addiction item=_helpers key=dependsOn}}
      <select name="_helpers_addiction-{{$dependsOn}}" size="1" onchange="pasteHelperContent(this)" style="display:none;">
        <option value="">&mdash; Choisir une aide</option>
        {{foreach from=$_helpers item=list_aides key=sTitleOpt}}
        <optgroup label="{{$sTitleOpt}}">
          {{html_options options=$list_aides}}
        </optgroup>
        {{/foreach}}
      </select>
      {{/foreach}}

      <input type="hidden" name="_hidden_addiction" value="" />
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CAddiction', this.form._hidden_addiction, 'addiction', this.form.type.value)">
        {{tr}}New{{/tr}}
      </button>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$addiction field=type}}</th>
    <td>
      {{mb_field object=$addiction field=type onchange="putHelperContent(this,'addiction')"}}
    </td>
		<td rowspan="2">
      <textarea name="addiction" onblur="if(verifNonEmpty(this)){submitAddiction(this.form);finAddiction(this.form);}"></textarea>
    </td>
		
  </tr>
  
  <tr>
    <td class="button" colspan="2">
      <button class="tick" type="button" onclick="if(verifNonEmpty(this.form.addiction)){submitAddiction(this.form);finAddiction(this.form);}">
        {{tr}}Add{{/tr}} une addiction
      </button>
    </td>
  </tr>
</table>
</form>