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
      {{mb_field object=$consult_anesth->_ref_operation field="operation_id" type="hidden" spec=""}}
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
      <label for="rques" title="Remarques concernant l'opération">Remarques pour le bloc</label>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_ref_operation->_aides.rques}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('COperation', this.form.rques)"></button><br />
      {{mb_field object=$consult_anesth->_ref_operation field="rques" onblur="submitFormAjax(this.form, 'systemMsg')"}}
      </form>
      <br />
      {{/if}}
      <form name="editAsaFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      {{mb_field object=$consult_anesth field="consultation_anesth_id" type="hidden" spec=""}}
      <label for="ASA" title="Score ASA">ASA</label>
      {{mb_field object=$consult_anesth field="ASA" defaultSelected="1" onchange="submitFormAjax(this.form, 'systemMsg')"}}
      <br /><br />
      <label for="premedication" title="Informations concernant la prémédication">Prémédication</label>
      <select name="_helpers_premedication" size="1" onchange="pasteHelperContent(this);this.form.premedication.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.premedication}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.premedication)"></button><br />
      {{mb_field object=$consult_anesth field="premedication" onchange="submitFormAjax(this.form, 'systemMsg')"}}
      
      <br /><br />
      <label for="prepa_preop" title="Informations concernant la préparation pré-opératoire">Préparation Pré-opératoire</label>
      <select name="_helpers_prepa_preop" size="1" onchange="pasteHelperContent(this);this.form.prepa_preop.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.prepa_preop}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.prepa_preop)"></button><br />
      {{mb_field object=$consult_anesth field="prepa_preop" onchange="submitFormAjax(this.form, 'systemMsg')"}}

      </form>
      <br />
      
      <form name="edittechniqueFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_technique_aed" />
      {{mb_field object=$consult_anesth field="consultation_anesth_id" type="hidden" spec=""}}
      <label for="technique" title="Ajouter une technique complementaire">Technique Complémentaire</label>
      <select name="_helpers_technique" size="1" onchange="pasteHelperContent(this)">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$techniquesComp->_aides.technique}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTechniqueComp', this.form._hidden_technique, 'technique')"></button><br />
      <input type="hidden" name="_hidden_technique" value="" />
      <textarea name="technique" onblur="if(verifNonEmpty(this)){submitTech(this.form);}"></textarea>
      <button class="submit" type="button" onclick="if(verifNonEmpty(this.form.technique)){submitTech(this.form);}">Ajouter</button>
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
      {{mb_field object=$consult field="consultation_id" type="hidden" spec=""}}
      {{mb_field object=$consult field="_check_premiere" type="hidden" spec=""}}
      <label for="rques" title="Remarques concernant la consultation">Remarques</label>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.rques}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.rques)"></button><br />
      {{mb_field object=$consult field="rques" onchange="submitFormAjax(this.form, 'systemMsg')"}}<br />
      </form>
    </td>
  </tr>
</table>