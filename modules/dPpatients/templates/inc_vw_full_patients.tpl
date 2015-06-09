{{if @$modules.dPImeds->mod_active}}
  {{mb_script module="Imeds" script="Imeds_results_watcher"}}
{{/if}}

{{mb_script module="compteRendu" script="modele_selector"}}

<script>
  var ViewFullPatient = {
    select: function(eLink) {
      // Unselect previous row
      if (this.idCurrent) {
        $(this.idCurrent).removeClassName("selected");
      }

      // Select current row
      this.idCurrent = $(eLink).up(1).identify();
      $(this.idCurrent).addClassName("selected");
    }
  };

  function popEtatSejour(sejour_id) {
    var url = new Url("hospi", "vw_parcours");
    url.addParam("sejour_id",sejour_id);
    url.pop(1000, 550, 'Etat du Séjour');
  }

  window.checkedMerge = [];
  checkOnlyTwoSelected = function(checkbox) {
    checkedMerge = checkedMerge.without(checkbox);

    if (checkbox.checked)
      checkedMerge.push(checkbox);

    if (checkedMerge.length > 2)
      checkedMerge.shift().checked = false;
  };

  function doMerge(oForm) {
    var operation_checkbox = $V(oForm["operation_ids[]"]);

    var checkboxs, object_class;
    if (operation_checkbox && operation_checkbox.length > 0) {
      checkboxs    = operation_checkbox;
      object_class = "COperation";
    }
    else {
      checkboxs    = $V(oForm["objects_id[]"]);
      object_class = "CSejour";
    }

    var url = new Url("system", "object_merger");
    url.addParam("objects_class", object_class);
    url.addParam("objects_id"   , checkboxs.join("-"));
    url.popup(800, 600, "merge_sejours");
  }

  onMergeComplete = function() {
    location.reload();
  };

  {{if $isImedsInstalled}}
    Main.add(function(){
      ImedsResultsWatcher.loadResults();
    });
  {{/if}}
</script>

<form name="fusion" action="?" method="get" onsubmit="return false;">

<table class="tbl" style="vertical-align: middle;">
  <tr>
    <th class="title text" colspan="3">
      <a href="#{{$patient->_guid}}" onclick="viewCompleteItem('{{$patient->_guid}}'); ViewFullPatient.select(this)">
        {{$patient->_view}} ({{$patient->_age}})
      </a>
    </th>
    <th class="title">
      {{if $patient->_canRead}}
      <div style="float:right;">
        {{if $isImedsInstalled}}
        <a href="#{{$patient->_guid}}" onclick="view_labo_patient()">
          <img align="top" src="images/icons/labo.png" title="Résultats de laboratoire" />
        </a>
        {{/if}}
        {{mb_include module=patients template=inc_form_docitems_button object=$patient}}
      </div>
      {{/if}}
    </th>
  </tr>

  {{if !$app->user_prefs.simpleCabinet}}
  <!-- Séjours -->
  <tr>
    <th colspan="4">
      {{if $can->admin}}
        <button type="button" class="merge notext compact" title="{{tr}}Merge{{/tr}}" style="float: left;" onclick="doMerge(this.form);">
          {{tr}}Merge{{/tr}}
        </button>
      {{/if}}
      {{tr}}CPatient-back-sejours{{/tr}}
      <small>({{$patient->_ref_sejours|@count}})</small>
      {{if !$vw_cancelled}}
        {{if $nb_ops_annulees || $nb_sejours_annules}}
          <br />
          <a class="button search" style="float: right" href="?m=patients&tab=vw_full_patients&patient_id={{$patient->_id}}&vw_cancelled=1"
             title="Voir {{if $nb_sejours_annules}}{{$nb_sejours_annules}} séjour(s) annulé(s){{if $nb_ops_annulees}} et {{/if}}{{/if}}{{if $nb_ops_annulees}}{{$nb_ops_annulees}} opération(s) annulée(s){{/if}}">
            Afficher les annulés
          </a>
        {{/if}}
      {{/if}}
    </th>
  </tr>
  <tbody id="sejours">
  {{foreach from=$patient->_ref_sejours item=_sejour}}
    {{if $_sejour->group_id == $g || $conf.dPpatients.CPatient.multi_group == "full"}}
      <tr id="CSejour-{{$_sejour->_id}}">
        <td class="narrow">
          <button class="lookup notext" onclick="popEtatSejour({{$_sejour->_id}});">{{tr}}Lookup{{/tr}}</button>
        </td>
        <td>
          {{if $can->admin}}
            <input type="checkbox" name="objects_id[]" value="{{$_sejour->_id}}" class="merge" style="float: left;"
              {{if $conf.alternative_mode}}onclick="checkOnlyTwoSelected(this)"{{/if}} />
          {{/if}}
          <a href="#{{$_sejour->_guid}}" onclick="{{if $can_view_dossier_medical}}loadSejour('{{$_sejour->_id}}');{{else}}viewCompleteItem('{{$_sejour->_guid}}');{{/if}} ViewFullPatient.select(this)"
            {{if $can->admin}}style="padding-right: 14px;"{{/if}}>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
              {{$_sejour->_shortview}}
            </span>
          </a>
        </td>

        {{assign var=praticien value=$_sejour->_ref_praticien}}
        <td {{if $_sejour->annule}}style="text-align: left;" class="cancelled"{{/if}}>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
        </td>

        <td style="text-align:right;">
        {{if $_sejour->_canRead}}
          {{if $isImedsInstalled}}
            <div onclick="view_labo_sejour({{$_sejour->_id}})" style="float: left;">
              {{mb_include module=Imeds template=inc_sejour_labo sejour=$_sejour link="#1"}}
            </div>
          {{/if}}

          <div style="clear: both;">
            {{mb_include module=patients template=inc_form_docitems_button object=$_sejour}}
          </div>
        {{/if}}
        </td>
      </tr>

      <!-- Parcours des consultation d'un séjour -->
      {{foreach from=$_sejour->_ref_consultations item=_consult}}
      <tr>
        <td colspan="2">
          <a class="iconed-text {{$_consult->_type}}" style="margin-left: 20px" href="#{{$_consult->_guid}}"
            onclick="viewCompleteItem('{{$_consult->_guid}}'); ViewFullPatient.select(this)">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}');">
              Consult. le {{$_consult->_datetime|date_format:$conf.date}}
            </span>
          </a>
        </td>

        {{assign var=praticien value=$_consult->_ref_chir}}

        {{if $_consult->annule}}
        <td style="text-align: left;" class="cancelled">
        {{else}}
        <td>
        {{/if}}
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
        </td>

        <td style="text-align: right;">
        {{if $_sejour->_canRead}}
          {{mb_include module=patients template=inc_form_docitems_button object=$_consult}}
         {{/if}}
        </td>
      </tr>
      {{/foreach}}

      {{foreach from=$_sejour->_ref_operations item=_op}}
      <tr>
        <td colspan="2">

          {{if $can->admin}}
            <input type="checkbox" name="operation_ids[]" class="merge" value="{{$_op->_id}}" style="float: left;"
                   {{if $conf.alternative_mode}}onclick="checkOnlyTwoSelected(this)"{{/if}} />
          {{/if}}

          <a href="#{{$_op->_guid}}" class="iconed-text interv" style="margin-left: 20px"
             onclick="viewCompleteItem('{{$_op->_guid}}'); ViewFullPatient.select(this)">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}')">
              Interv. le {{$_op->_datetime|date_format:$conf.date}}
            </span>
          </a>
        </td>

        {{assign var=praticien value=$_op->_ref_chir}}
        <td {{if $_op->annulee}}style="text-align: left;" class="cancelled"{{/if}}>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
        </td>

        <td style="text-align:right;">
          {{if $_op->_canRead}}
            {{mb_include module=patients template=inc_form_docitems_button object=$_op}}
          {{/if}}
        </td>
      </tr>

      {{assign var="consult_anesth" value=$_op->_ref_consult_anesth}}
      {{if $consult_anesth->_id}}
      {{assign var="_consult" value=$consult_anesth->_ref_consultation}}
      <tr>
        <td colspan="2" style="padding-left: 20px;">
          <a href="#{{$consult_anesth->_guid}}" class="iconed-text anesth" style="margin-left: 20px"
             onclick="viewCompleteItem('{{$consult_anesth->_guid}}'); ViewFullPatient.select(this)">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$consult_anesth->_guid}}')">
              Consult le {{$_consult->_datetime|date_format:$conf.date}}
            </span>
          </a>
        </td>

        {{assign var=praticien value=$_consult->_ref_chir}}
        {{if $_consult->annule}}
        <td style="text-align: left;" class="cancelled">[Consult annulée]</td>
        {{else}}
        <td>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
        </td>
        {{/if}}

        <td style="text-align:right;">
        {{if $_consult->_canRead}}
          {{mb_include module=patients template=inc_form_docitems_button object=$consult_anesth}}
        {{/if}}
        </td>
      </tr>
      {{/if}}
      {{/foreach}}
    {{elseif $conf.dPpatients.CPatient.multi_group == "limited" && !$_sejour->annule}}
      <tr>
        <td colspan="2">
          {{$_sejour->_shortview}}
        </td>
        <td colspan="2" style="background-color:#afa">
          {{$_sejour->_ref_group->text|upper}}
        </td>
      </tr>
    {{/if}}
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CPatient-back-sejours.empty{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
  {{/if}}

  <!-- Consultations -->

  <tr>
    <th colspan="4">
      {{tr}}CPatient-back-consultations{{/tr}}
      <small>({{$patient->_ref_consultations|@count}})</small>
      {{if !$vw_cancelled && $nb_consults_annulees}}
        <br />
        <a class="button search" style="float: right" href="?m=patients&tab=vw_full_patients&patient_id={{$patient->_id}}&vw_cancelled=1"
           title="Voir {{$nb_consults_annulees}} consultation(s) annulée(s)">
          Afficher les annulées
        </a>
      {{/if}}
    </th>
  </tr>

  <tbody id="consultations">

  {{foreach from=$patient->_ref_consultations item=_consult}}
    {{if $_consult->_ref_chir->_ref_function->group_id == $g || $conf.dPpatients.CPatient.multi_group == "full"}}
    <tr>
      <td colspan="2">
        <a href="#{{$_consult->_guid}}" class="iconed-text {{$_consult->_type}}"
           onclick="viewCompleteItem('{{$_consult->_guid}}'); ViewFullPatient.select(this)">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}');">
            Consult. le {{$_consult->_datetime|date_format:$conf.date}}
          </span>
        </a>
      </td>

      {{assign var=praticien value=$_consult->_ref_chir}}

      <td {{if $_consult->annule}}style="text-align: left;" class="cancelled"{{/if}}>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
      </td>

      <td style="text-align:right;">
      {{if $_consult->_canRead}}
        {{if $_consult->_type === "anesth"}}
          {{foreach from=$_consult->_refs_dossiers_anesth item=_dossier_anesth name=foreach_anesth}}
            {{mb_include module=patients template=inc_form_docitems_button object=$_dossier_anesth}}
          {{/foreach}}
        {{else}}
          {{mb_include module=patients template=inc_form_docitems_button object=$_consult}}
        {{/if}}
      {{/if}}
      </td>
    </tr>
    {{elseif $conf.dPpatients.CPatient.multi_group == "limited" && !$_consult->annule}}
    <tr>
      <td colspan="2">
        <span class="iconed-text">Le {{$_consult->_datetime|date_format:$conf.datetime}}</span>
      </td>
      <td style="background-color:#afa" colspan="2">
        {{$_consult->_ref_chir->_ref_function->_ref_group->text|upper}}
      </td>
    </tr>
    {{/if}}
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CPatient-back-consultations.empty{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>

</form>

<hr/>
  
<!-- Planifier -->
<table class="tbl">
  <tr>
    <th class="title">Planifier</th>
  </tr>

  <tbody id="planifier">
    <tr>
      <td class="button">
        {{math assign=ecap_dhe equation="a * b" a='ecap'|module_active|strlen b=$current_group|idex:'ecap'|strlen}}
        {{if $ecap_dhe}}
          {{mb_include module=ecap template=inc_button_dhe patient_id=$patient->_id praticien_id=""}}
        {{else}}
          {{if !$app->user_prefs.simpleCabinet}}
            {{if $canPlanningOp->edit}}
              <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
                {{tr}}COperation{{/tr}}
              </a>
            {{/if}}
            {{if $canPlanningOp->read}}
              <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
                Interv. hors plage
              </a>
              <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->_id}}&amp;sejour_id=0">
                {{tr}}CSejour{{/tr}}
              </a>
            {{/if}}
          {{/if}}
        {{/if}}
      </td>
    </tr>
    <tr>
      <td class="button">
        {{if $canCabinet->read}}
          <a class="button new" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->_id}}&amp;consultation_id=0">
            {{tr}}CConsultation{{/tr}}
          </a>

          {{mb_include module="cabinet" template="inc_button_consult_immediate" patient_id=$patient->_id}}
        {{/if}}
      </td>
    </tr>
  </tbody>
</table>
