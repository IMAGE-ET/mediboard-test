<script type="text/javascript">
function submitTech(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadListTech});
  oForm.reset();
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
      </form>
      <br />
      {{/if}}
      <form name="editAsaFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <label for="ASA" title="Score ASA">ASA</label>
      <select name="ASA" onchange="submitFormAjax(this.form, 'systemMsg')">
        {{html_options values=$consult_anesth->_enums.ASA output=$consult_anesth->_enums.ASA selected=$consult_anesth->ASA}}
      </select>
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
      </select><br />
      <textarea name="technique"></textarea>
      <button class="submit" type="button" onclick="submitTech(this.form);">Ajouter</button>
      </form>
    </td>
    <td class="text" rowspan="2" id="listTech">
      {{include file="inc_consult_anesth/techniques_comp.tpl"}}
    </td>
  </tr>
  <tr>
    <td>
      <form class="watch" name="editFrmRemarques" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      <input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
      <input type="hidden" name="_check_premiere" value="{{$consult->_check_premiere}}" />
      <label for="rques" title="Remarques concernant la consultation">Remarques</label>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this)">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.rques}}
      </select><br />
      <textarea name="rques" rows="5">{{$consult->rques}}</textarea><br />
      <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg')">
        sauver
      </button>
      </form>
    </td>
  </tr>
</table>