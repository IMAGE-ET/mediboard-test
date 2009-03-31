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
  UrllistTech.requestUpdate('listTech', { waitingText : null} );
}
</script>

<table class="form">
  <tr>
    <td class="text">
      {{if $consult_anesth->operation_id}}
      <form name="editOpFrm" action="?m=dPcabinet" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_planning_aed" />
      {{mb_field object=$consult_anesth->_ref_operation field="operation_id" hidden=1 prop=""}}
      {{mb_label object=$consult_anesth->_ref_operation field="type_anesth"}}
      <select name="type_anesth" onchange="submitFormAjax(this.form, 'systemMsg')">
        <option value="">&mdash; Choisir un type d'anesthésie</option>
        {{foreach from=$anesth item=curr_anesth}}
          <option value="{{$curr_anesth->type_anesth_id}}" {{if $consult_anesth->_ref_operation->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
            {{$curr_anesth->name}}
          </option>
        {{/foreach}}
      </select>
      <br />
      {{mb_label object=$consult_anesth->_ref_operation field="rques"}}
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_ref_operation->_aides.rques.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('COperation', this.form.rques)">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult_anesth->_ref_operation field="rques" onblur="submitFormAjax(this.form, 'systemMsg')"}}
      </form>
      
      <br />
      {{/if}}
      

      <form name="editAsaFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
      {{mb_label object=$consult_anesth field="ASA"}}
      {{mb_field object=$consult_anesth field="ASA" defaultOption="&mdash; Veuillez Choisir" onchange="submitFormAjax(this.form, 'systemMsg')"}}
		  {{mb_label object=$consult_anesth field="position"}}
		  {{mb_field object=$consult_anesth field="position" defaultOption="&mdash; Veuillez Choisir" onchange="submitFormAjax(this.form, 'systemMsg')"}}
      
      {{if !$isPrescriptionInstalled || $dPconfig.dPcabinet.CConsultAnesth.view_premedication}}
      <br />
      {{mb_label object=$consult_anesth field="premedication"}}
      <select name="_helpers_premedication" size="1" onchange="pasteHelperContent(this);this.form.premedication.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.premedication.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.premedication)">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult_anesth field="premedication" onchange="submitFormAjax(this.form, 'systemMsg')"}}
      {{else}}
      <br />
      <br />
       <button class="tick" type="button" onclick="tabsConsultAnesth.setActiveTab('prescription_sejour')">Accéder à la prescription</button>
       
      {{/if}}
      
      
      <br />
      {{mb_label object=$consult_anesth field="prepa_preop"}}
      <select name="_helpers_prepa_preop" size="1" onchange="pasteHelperContent(this);this.form.prepa_preop.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.prepa_preop.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.prepa_preop)">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult_anesth field="prepa_preop" onchange="submitFormAjax(this.form, 'systemMsg')"}}
      </form>
      

      <br />
      <form name="edittechniqueFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_technique_aed" />
      {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
      {{mb_label object=$techniquesComp field="technique"}}
      <select name="_helpers_technique" size="1" onchange="pasteHelperContent(this)">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$techniquesComp->_aides.technique.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTechniqueComp', this.form._hidden_technique, 'technique')">{{tr}}New{{/tr}}</button><br />
      <input type="hidden" name="_hidden_technique" value="" />
      <textarea name="technique" onblur="if(!$(this).emptyValue()){submitTech(this.form);}"></textarea>
      <button class="submit" type="button">Ajouter</button>
      </form>
    </td>
    
    <td class="text" rowspan="2" id="listTech">
      {{include file="../../dPcabinet/templates/inc_consult_anesth/techniques_comp.tpl"}}
    </td>
  </tr>
  <tr>
    <td>
      <form class="watch" name="editFrmRemarques" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
      {{mb_label object=$consult field="rques"}}
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.rques.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.rques)">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult field="rques" onchange="submitFormAjax(this.form, 'systemMsg')"}}<br />
      </form>
    </td>
  </tr>
</table>