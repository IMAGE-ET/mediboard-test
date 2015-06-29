{{mb_default var="view_prescription" value=1}}

<script>
  submitTech = function(oForm) {
    onSubmitFormAjax(oForm, reloadListTech);
    if ($V(oForm.elements.del)) {
      oForm.reset();
    }
    return false;
  };

  Main.add(function() {
    guessScoreApfel();
  });

  reloadListTech = function() {
    var UrllistTech = new Url("dPcabinet", "httpreq_vw_list_techniques_comp");
    UrllistTech.addParam("selConsult", "{{$consult->_id}}");
    UrllistTech.addParam("dossier_anesth_id", "{{$consult_anesth->_id}}");
    UrllistTech.requestUpdate('listTech', callbackInfoAnesth);
  };

  guessScoreApfel = function() {
    var url = new Url("cabinet", "ajax_guess_score_apfel");
    url.addParam("patient_id", "{{$consult->patient_id}}");
    url.addParam("consult_id", "{{$consult_anesth->_id}}");
    url.requestUpdate("score_apfel_area", function() {
      return getForm('editScoreApfel').onsubmit();
    });
  };

  afterStoreScore = function(id, obj) {
    $("score_apfel").update(obj._score_apfel);
    callbackInfoAnesth();
  };

  toggleUSCPO = function(status) {
    var form = getForm("editOpAnesthFrm");
    if (status == 1) {
      $("uscpo_area").setStyle({visibility: "visible"});
    }
    else {
      $("uscpo_area").setStyle({visibility: "hidden"});
    }
    // Permet de valuer à 1 automatiquement la durée uscpo,
    // ou bien 0 si le passage uscpo est repassé à non.
    $V(form.duree_uscpo, status);
  };

  checkUSCPO = function() {
    var form = getForm("editOpAnesthFrm");
    if ($V(form._passage_uscpo) == 1 && $V(form.duree_uscpo) == "") {
      alert("Veuillez saisir une durée USCPO");
      return false;
    }

    return true;
  };

  callbackInfoAnesth = function() {
    if (!window.tabsConsultAnesth) {
      return;
    }

    var count = 0;

    var form = getForm("editOpAnesthFrm");
    var fields = ["rques", "passage_uscpo", "type_anesth", "ASA", "position"];

    fields.each(function(field) {
      if ($V(form.elements[field])) {
        count++;
      }
    });

    if ($V(getForm("editInfosAnesthFrm").prepa_preop)) {
      count++;
    }

    count += $("listTech").select("button.trash").length;

    if ($V(getForm("editRquesConsultFrm").rques)) {
      count++;
    }

    var form = getForm("editScoreApfel");

    form.select("input[type=checkbox").each(function(input) {
      if (input.checked) {
        count++;
      }
    });

    Control.Tabs.setTabCount("InfoAnesth", count);
  };
</script>

{{assign var=operation value=$consult_anesth->_ref_operation}}

<table class="form">
  <tr>
    <td colspan="2">
      <fieldset>
        <legend id="didac_legend_intervention">Intervention</legend>
        {{mb_ternary var=object test=$operation->_id value=$operation other=$consult_anesth}}
        {{mb_ternary var=dosql test=$operation->_id value='do_planning_aed' other='do_consult_anesth_aed'}}
        {{mb_ternary var=module test=$operation->_id value='planningOp' other='cabinet'}}
        <form name="editOpAnesthFrm" method="post" onsubmit="{{if $conf.dPplanningOp.COperation.show_duree_preop == 2}}if (checkUSCPO()) {{/if}} return onSubmitFormAjax(this);"">
          <input type="hidden" name="m" value="{{$module}}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="{{$dosql}}" />
          <input type="hidden" name="callback" value="callbackInfoAnesth" />
          {{mb_key object=$object}}

          <div style="width: 50%; float: left;">
            {{mb_label object=$object field="rques"}}
            {{mb_field object=$object field="rques" rows="4" onblur="this.form.onsubmit()" form="editOpAnesthFrm"
            aidesaisie="validateOnBlur: 0"}}
          </div>
          <div style="width: 49%; float: right;">
            {{if $conf.dPplanningOp.COperation.show_duree_uscpo >= 1}}
              <div>
                {{mb_label object=$object field=passage_uscpo}}
                {{mb_field object=$object field=passage_uscpo onclick="toggleUSCPO(\$V(this)); this.form.onsubmit();"}}

                <span id="uscpo_area" {{if !$object->passage_uscpo}}style="visibility: hidden;"{{/if}}>
                  {{mb_label object=$object field=duree_uscpo style="padding-left: 1.4em;" id="uscpo_label"}}
                  {{mb_field object=$object field=duree_uscpo form=editOpAnesthFrm increment=true onblur="this.form.onsubmit()"}} nuit(s)
                </span>
              </div>
            {{/if}}
            {{mb_label object=$object field=type_anesth}}
            <select name="type_anesth" onchange="this.form.onsubmit()" style="width: 12em;">
              <option value="">&mdash; Anesthésie</option>
              {{foreach from=$anesth item=curr_anesth}}
                {{if $curr_anesth->actif || $object->type_anesth == $curr_anesth->type_anesth_id}}
                  <option value="{{$curr_anesth->type_anesth_id}}" {{if $object->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}}>
                    {{$curr_anesth->name}} {{if !$curr_anesth->actif && $object->type_anesth == $curr_anesth->type_anesth_id}}(Obsolète){{/if}}
                  </option>
                {{/if}}
              {{/foreach}}
            </select>
            <br />
            {{mb_label object=$object field="ASA" style="padding-left: 6em;"}}
            {{mb_field object=$object field="ASA" emptyLabel="Choose" style="width: 12em;" onchange="this.form.onsubmit()"}}
            <br />
            {{mb_label object=$object field="position" style="padding-left: 4.5em;"}}
            {{mb_field object=$object field="position" emptyLabel="Choose" style="width: 12em;" onchange="this.form.onsubmit()"}}
          </div>
        </form>
      </fieldset>
      <fieldset>
        <legend>Pré-opératoire</legend>
        <form name="editInfosAnesthFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="m" value="dPcabinet" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
          <input type="hidden" name="callback" value="callbackInfoAnesth" />
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
                  {{if "dPcabinet CPrescription view_prescription"|conf:"CGroups-$g"}}
                    {{if $view_prescription}}
                      {{mb_label object=$consult_anesth field="premedication"}}
                      <br />
                      <button class="tick" type="button" onclick="tabsConsultAnesth.setActiveTab('prescription_sejour');
                        tabsConsultAnesth.activeLink.up('li').onmousedown()">Accéder à la prescription</button>
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
                <button class="add" type="button" onclick="if ($V(this.form.technique)) { this.form.onsubmit() }">{{tr}}Add{{/tr}}</button>
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
      <form name="editRquesConsultFrm" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="cabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_consultation_aed" />
        <input type="hidden" name="callback" value="callbackInfoAnesth" />
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
        <legend>Score APFEL</legend>
        <div id="score_apfel_area">
          {{mb_include module=cabinet template=inc_guess_score_apfel}}
        </div>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td style="width: 50%;">
      <form name="editStratAntibioConsultFrm" method="post" onsubmit="return onSubmitFormAjax(this);">
        {{mb_class object=$consult_anesth}}
        {{mb_key   object=$consult_anesth}}
        <input type="hidden" name="callback" value="callbackInfoAnesth" />
        <fieldset>
          <legend>{{mb_label object=$consult_anesth field="strategie_antibio"}}</legend>
          {{mb_field object=$consult_anesth field="strategie_antibio" rows="4" onblur="this.form.onsubmit()" form="editStratAntibioConsultFrm"
          aidesaisie="validateOnBlur: 0"}}
        </fieldset>
      </form>
    </td>
    <td></td>
  </tr>
</table>