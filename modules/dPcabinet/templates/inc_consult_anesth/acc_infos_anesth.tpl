{{mb_default var="view_prescription" value=1}}

<script type="text/javascript">
function submitTech(oForm) {
  onSubmitFormAjax(oForm, { onComplete : reloadListTech });
  if ($V(oForm.elements.del)) {
    oForm.reset();
  }
  return false;
}

function reloadListTech() {
  var UrllistTech = new Url("dPcabinet", "httpreq_vw_list_techniques_comp");
  UrllistTech.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  UrllistTech.requestUpdate('listTech');
}

guessScoreApfel = function() {
  var url = new Url("dPcabinet", "ajax_guess_score_apfel");
  url.addParam("patient_id", "{{$consult->patient_id}}");
  url.addParam("consult_id", "{{$consult_anesth->_id}}");
  url.requestUpdate("score_apfel_area", {onComplete: function() {
    return getForm('editScoreApfel').onsubmit();
  }});
}

afterStoreScore = function(id, obj) {
  $("score_apfel").update(obj._score_apfel);
}

toggleUSCPO = function(status) {
  var form = getForm("editTypeAnesthFrm");
  if (status == 1) {
    $("uscpo_area").setStyle({visibility: "visible"});
  }
  else {
    $("uscpo_area").setStyle({visibility: "hidden"});
  }
  // Permet de valuer à 1 automatiquement la durée uscpo,
  // ou bien 0 si le passage uscpo est repassé à non.
  $V(form.duree_uscpo, status);
}

checkUSCPO = function() {
  var form = getForm("editTypeAnesthFrm");
  if ($V(form._passage_uscpo) == 1 && $V(form.duree_uscpo) == "") {
    alert("Veuillez saisir une durée USCPO");
    return false;
  }
  
  return true; 
}

</script>

{{assign var=operation value=$consult_anesth->_ref_operation}}

<table class="form">
  <tr>
    <td colspan="2">
      <fieldset>
        <legend>Intervention</legend>
        <table class="layout main">
          <tr>
            <td class="halfPane">
              {{if $operation->_id}}
              <form name="editOpAnesthFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitFormAjax(this);">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              {{mb_key object=$operation}}
              {{mb_label object=$operation field="rques"}}
              {{mb_field object=$operation field="rques" rows="4" onblur="this.form.onsubmit()" form="editOpAnesthFrm"
                  aidesaisie="validateOnBlur: 0"}}
              </form>
              {{else}}
              <div class="small-info text">
                Aucune intervention n'étant selectionné, vous ne pouvez pas accéder
                à la totalité des champs disponibles pour la consultation
              </div>
              {{/if}}
            </td>
            <td class="halfPane">
              {{if $operation->_id}}
                <form name="editTypeAnesthFrm" action="?m=dPcabinet" method="post"
                  onsubmit="{{if $conf.dPplanningOp.COperation.show_duree_preop == 2}}if (checkUSCPO()) {{/if}} return onSubmitFormAjax(this);">
                  <input type="hidden" name="m" value="dPplanningOp" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="dosql" value="do_planning_aed" />
                  {{mb_key object=$operation}}
                  {{if $conf.dPplanningOp.COperation.show_duree_uscpo >= 1}}
                    <div>
                      {{mb_label object=$operation field=passage_uscpo}}
                      {{mb_field object=$operation field=passage_uscpo onclick="toggleUSCPO(\$V(this)); this.form.onsubmit();"}}
                      
                      <span id="uscpo_area" {{if !$operation->passage_uscpo}}style="visibility: hidden;"{{/if}}>
                        {{mb_label object=$operation field=duree_uscpo style="padding-left: 1.4em;" id="uscpo_label"}}
                        {{mb_field object=$operation field=duree_uscpo form=editTypeAnesthFrm increment=true onblur="this.form.onsubmit()"}} nuit(s)
                      </span>
                    </div>
                  {{/if}}
                  {{mb_label object=$operation field=type_anesth}}
                  <select name="type_anesth" onchange="this.form.onsubmit()" style="width: 12em;">
                    <option value="">&mdash; Anesthésie</option>
                    {{foreach from=$anesth item=curr_anesth}}
                      {{if $curr_anesth->actif || $operation->type_anesth == $curr_anesth->type_anesth_id}}
                        <option value="{{$curr_anesth->type_anesth_id}}" {{if $operation->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}}>
                          {{$curr_anesth->name}} {{if !$curr_anesth->actif && $operation->type_anesth == $curr_anesth->type_anesth_id}}(Obsolète){{/if}}
                        </option>
                      {{/if}}
                    {{/foreach}}
                  </select>
                  <br />
                </form>
              {{/if}}
              <form name="editInfosASAFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
                <input type="hidden" name="m" value="dPcabinet" />
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
                {{mb_key object=$consult_anesth}}
                {{mb_label object=$consult_anesth field="ASA" style="padding-left: 6em;"}}
                {{mb_field object=$consult_anesth field="ASA" emptyLabel="Choose" style="width: 12em;" onchange="this.form.onsubmit()"}}
                <br />
                {{mb_label object=$consult_anesth field="position" style="padding-left: 4.5em;"}}
                {{mb_field object=$consult_anesth field="position" emptyLabel="Choose" style="width: 12em;" onchange="this.form.onsubmit()"}}
              </form>
            </td>
          </tr>
        </table>
      </fieldset>
      <fieldset>
        <legend>Pré-opératoire</legend>
        <form name="editInfosAnesthFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="m" value="dPcabinet" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
          {{mb_key object=$consult_anesth}}
          <table class="layout main">
            <tr>
              <td class="halfPane">
                {{mb_label object=$consult_anesth field="prepa_preop"}}
                {{mb_field object=$consult_anesth field="prepa_preop" rows="4" onchange="this.form.onsubmit()" form="editInfosAnesthFrm"
                  aidesaisie="validateOnBlur: 0"}}
              </td>
              <td class="halfPane">
                {{if !$isPrescriptionInstalled || ($conf.dPcabinet.CConsultAnesth.view_premedication && $app->user_prefs.displayPremedConsult)}}
                  {{mb_label object=$consult_anesth field="premedication"}}
                  {{mb_field object=$consult_anesth field="premedication" rows="4" onchange="this.form.onsubmit()" form="editInfosAnesthFrm"
                  aidesaisie="validateOnBlur: 0"}}
                {{else}}
                  {{if $conf.dPcabinet.CPrescription.view_prescription}}
                    {{if $view_prescription}}
                      {{mb_label object=$consult_anesth field="premedication"}}
                      <br />
                      <button class="tick" type="button" onclick="tabsConsultAnesth.setActiveTab('prescription_sejour')">Accéder à la prescription</button>
                    {{/if}}
                  {{else}}
                    <div class="small-info">
                      La saisie de la prémédication n'est actuellement pas active
                    </div>
                  {{/if}}
                {{/if}}
              </td>
            </tr>
          </table>
        </form>
      </fieldset>
      
      <fieldset>
        <legend>{{mb_label object=$techniquesComp field="technique"}}</legend>
        <table class="layout main">
          <tr>
            <td class="halfPane">
              <form name="addEditTechCompFrm" action="?m=dPcabinet" method="post" onsubmit="return submitTech(this)">
                <input type="hidden" name="m" value="dPcabinet" />
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_technique_aed" />
                {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1}}
                {{mb_field object=$techniquesComp field="technique" rows="4" form="addEditTechCompFrm"
                  aidesaisie="validateOnBlur: 0"}}
                <button class="add" type="submit">{{tr}}Add{{/tr}}</button>
              </form>
            </td>
            <td class="halfPane text" id="listTech">
              {{mb_include module=cabinet template=inc_consult_anesth/techniques_comp}}
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td style="width: 50%;">
      <form name="editRquesConsultFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
  
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      {{mb_key object=$consult}}
      <fieldset>
        <legend>{{mb_label object=$consult field="rques"}}</legend>
        {{mb_field object=$consult field="rques" rows="4" onblur="this.form.onsubmit()" form="editRquesConsultFrm"
                  aidesaisie="validateOnBlur: 0"}}
      </fieldset>
      </form>
    </td>
    <td>
      <fieldset>
        <legend>Score APFEL <button type="button" class="tick" onclick="guessScoreApfel()">Evaluer</button></legend>
        <div id="score_apfel_area">
          {{mb_include module=cabinet template=inc_guess_score_apfel}}
        </div>
      </fieldset>
    </td>
  </tr>
</table>