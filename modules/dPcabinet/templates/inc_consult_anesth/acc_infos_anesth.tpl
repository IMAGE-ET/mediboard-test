<script type="text/javascript">

function submitTech(oForm) {
  if(oForm.technique){
    var technique = oForm.technique.value;
  }
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadListTech});
  oForm.reset();
  if(oForm.technique){
    oForm._hidden_technique.value = technique;
  }
}

function reloadListTech() {
  var UrllistTech = new Url;
  UrllistTech.setModuleAction("dPcabinet", "httpreq_vw_list_techniques_comp");
  UrllistTech.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  UrllistTech.requestUpdate('listTech');
}
</script>

<table class="form">
  <tr>
    <td class="text">
    	{{assign var=operation value=$consult_anesth->_ref_operation}}
      {{if $operation->_id}}
      <form name="editOpFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitFormAjax(this);">
      	
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_planning_aed" />
      {{mb_key object=$operation}}

      {{mb_label object=$operation field=type_anesth}}
      {{mb_field object=$operation field=type_anesth options=$anesth onchange="this.form.onsubmit()"}}

      <br />
      {{mb_label object=$operation field="rques"}}
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();" class="helper">
        <option value="">&mdash; Aide</option>
        {{html_options options=$operation->_aides.rques.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('COperation', this.form.rques)">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$operation field="rques" onblur="this.form.onsubmit()"}}
      </form>
      
      <br />
      {{/if}}
      
      <form name="editAsaFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
      	
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      {{mb_key object=$consult_anesth}}
      {{mb_label object=$consult_anesth field="ASA"}}
      {{mb_field object=$consult_anesth field="ASA" emptyLabel="Choose" onchange="this.form.onsubmit()"}}
		  {{mb_label object=$consult_anesth field="position"}}
		  {{mb_field object=$consult_anesth field="position" emptyLabel="Choose" onchange="this.form.onsubmit()"}}
      
      {{if !$isPrescriptionInstalled || $dPconfig.dPcabinet.CConsultAnesth.view_premedication}}
      <br />
      {{mb_label object=$consult_anesth field="premedication"}}
      <select name="_helpers_premedication" size="1" onchange="pasteHelperContent(this);this.form.premedication.onchange();" class="helper">
        <option value="">&mdash; Aide</option>
        {{html_options options=$consult_anesth->_aides.premedication.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.premedication, '', '', '', '', {{$userSel->_id}})">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult_anesth field="premedication" onchange="this.form.onsubmit()"}}
      {{else}}
      <br />
      <br />
				{{if $dPconfig.dPcabinet.CPrescription.view_prescription}}
	        <button class="tick" type="button" onclick="tabsConsultAnesth.setActiveTab('prescription_sejour')">Accéder à la prescription</button>
	      {{/if}}
      {{/if}}
      
      <br />
      {{mb_label object=$consult_anesth field="prepa_preop"}}
      <select name="_helpers_prepa_preop" size="1" onchange="pasteHelperContent(this);this.form.prepa_preop.onchange();" class="helper">
        <option value="">&mdash; Aide</option>
        {{html_options options=$consult_anesth->_aides.prepa_preop.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.prepa_preop, '', '', '', '', {{$userSel->_id}})">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult_anesth field="prepa_preop" onchange="this.form.onsubmit()"}}
			
      </form>
      
      <br />
      
			<form name="edittechniqueFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      	
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_technique_aed" />
			
      {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1}}
      {{mb_label object=$techniquesComp field="technique"}}
      
			<select name="_helpers_technique" size="1" onchange="pasteHelperContent(this)" class="helper">
        <option value="">&mdash; Aide</option>
        {{html_options options=$techniquesComp->_aides.technique.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTechniqueComp', this.form._hidden_technique, 'technique', '', '', '', {{$userSel->_id}})">{{tr}}New{{/tr}}</button><br />
      
			<input type="hidden" name="_hidden_technique" value="" />
      <textarea name="technique" onblur="if(!$(this).emptyValue()){ submitTech(this.form);}"></textarea>
      <button class="submit" type="button">{{tr}}Add{{/tr}}</button>

      </form>
    </td>
    
    <td class="text" rowspan="2" id="listTech">
      {{mb_include module=dPcabinet template=inc_consult_anesth/techniques_comp}}
    </td>
  </tr>

  <tr>
    <td>
      <form class="watch" name="editFrmRemarques" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
  
	    <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      {{mb_key object=$consult}}
			
      {{mb_label object=$consult field="rques"}}
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();" class="helper">
        <option value="">&mdash; Aide</option>
        {{html_options options=$consult->_aides.rques.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.rques, '', '', '', '', {{$userSel->_id}})">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult field="rques" onchange="this.form.onsubmit()"}}
	
	    </form>
    </td>
  </tr>
</table>