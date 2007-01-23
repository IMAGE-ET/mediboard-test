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
  var UrllistTech= new Url;
  UrllistTech.setModuleAction("dPcabinet", "httpreq_vw_list_techniques_comp");
  UrllistTech.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  UrllistTech.requestUpdate('listTech', { waitingText : null});
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
      <input type="hidden" name="operation_id" value="{{$consult_anesth->_ref_operation->operation_id}}" />
      <label for="type_anesth" title="Type d'anesthésie pour l'intervention">Type d'anesthésie</label>
      <select name="type_anesth" onchange="submitFormAjax(this.form, 'systemMsg')">
        <option value="">&mdash; Choisir un type d'anesthésie</option>
        {{foreach from=$anesth item=curr_anesth}}
          <option value="{{$curr_anesth->type_anesth_id}}" {{if $consult_anesth->_ref_operation->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
            {{$curr_anesth->name}}
          </option>
        {{/foreach}}
      </select>
      <br />
      <label for="rques" title="Remarques concernant l'opération">Remarques</label>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_ref_operation->_aides.rques}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('COperation', this.form.rques)"></button><br />
      <textarea name="rques" onblur="submitFormAjax(this.form, 'systemMsg')">{{$consult_anesth->_ref_operation->rques}}</textarea>
      </form>
      <br />
      {{/if}}
      <form name="editAsaFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <label for="ASA" title="Score ASA">ASA</label>
      {{if $consult_anesth->ASA}}
      {{assign var="selected" value=$consult_anesth->ASA}}
      {{else}}
      {{assign var="selected" value=$consult_anesth->_enums.ASA.0}}
      {{/if}}
      {{html_options name="ASA" options=$consult_anesth->_enumsTrans.ASA selected=$selected onchange="submitFormAjax(this.form, 'systemMsg')"}}
      <br /><br />
      <label for="premedication" title="Informations concernant la prémédication">Prémédication</label>
      <select name="_helpers_premedication" size="1" onchange="pasteHelperContent(this);this.form.premedication.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.premedication}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.premedication)"></button><br />
      <textarea name="premedication" onchange="submitFormAjax(this.form, 'systemMsg')">{{$consult_anesth->premedication}}</textarea>
      
      <br /><br />
      <label for="prepa_preop" title="Informations concernant la préparation pré-opératoire">Préparation Pré-opératoire</label>
      <select name="_helpers_prepa_preop" size="1" onchange="pasteHelperContent(this);this.form.prepa_preop.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.prepa_preop}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.prepa_preop)"></button><br />
      <textarea name="prepa_preop" onchange="submitFormAjax(this.form, 'systemMsg')">{{$consult_anesth->prepa_preop}}</textarea>

      </form>
      <br />
      
      <form name="edittechniqueFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_technique_aed" />
      <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <label for="technique" title="Ajouter une technique complementaire">Technique Complémentaire</label>
      <select name="_helpers_technique" size="1" onchange="pasteHelperContent(this)">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$techniquesComp->_aides.technique}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTechniqueComp', this.form._hidden_technique, 'technique')"></button><br />
      <input type="hidden" name="_hidden_technique" value="" />
      <textarea name="technique" onblur="if(this.value!=''){submitTech(this.form);}"></textarea>
      <button class="submit" type="button" onclick="if(this.form.technique.value!=''){submitTech(this.form);}">Ajouter</button>
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
      <input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
      <input type="hidden" name="_check_premiere" value="{{$consult->_check_premiere}}" />
      <label for="rques" title="Remarques concernant la consultation">Remarques</label>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.rques}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.rques)"></button><br />
      <textarea name="rques" onchange="submitFormAjax(this.form, 'systemMsg')">{{$consult->rques}}</textarea><br />
      </form>
    </td>
  </tr>
</table>