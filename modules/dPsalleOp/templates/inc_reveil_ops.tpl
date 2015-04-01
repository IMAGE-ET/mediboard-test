<script>
  Main.add(function() {
    Control.Tabs.setTabCount("ops", "{{$listOperations|@count}}");

    $('heure').innerHTML = "{{$hour|date_format:$conf.time}}";

    {{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}
  });

  orderTabops = function(col, way) {
    orderTabReveil(col, way, 'ops');
  };
  
  // faire le submit de formOperation dans le onComplete de l'ajax
  checkPersonnel = function(oFormAffectation, oFormOperation) {
    oFormOperation.entree_reveil.value = 'current';
    // si affectation renseignée, on submit les deux formulaires
    if (oFormAffectation && $V(oFormAffectation.personnel_id) != "") {
      onSubmitFormAjax(oFormAffectation, onSubmitFormAjax.curry(oFormOperation, refreshTabsReveil));
    }
    else {
    // sinon, on ne submit que l'operation
      onSubmitFormAjax(oFormOperation, refreshTabsReveil);
    }
  };
</script>

{{assign var=use_poste value=$conf.dPplanningOp.COperation.use_poste}}

<table class="tbl">
  <tr>
    <th>{{mb_colonne class="COperation" field="salle_id" order_col=$order_col order_way=$order_way function=orderTabops}}</th>
    <th>{{mb_colonne class="COperation" field="chir_id" order_col=$order_col order_way=$order_way function=orderTabops}}</th>
    <th>{{mb_colonne class="COperation" field="_patient" order_col=$order_col order_way=$order_way function=orderTabops}} <input type="text" name="_seek_patient_preop" value="" class="seek_patient" onkeyup="seekPatient(this);" onchange="seekPatient(this);" /></th>
    <th class="narrow">Dossier</th>
    {{if $use_poste}}
      <th>{{tr}}SSPI.Poste{{/tr}}</th>
    {{/if}}
    {{if $isbloodSalvageInstalled}}
      <th>{{tr}}SSPI.RSPO{{/tr}}</th>
    {{/if}}
    <th>{{mb_colonne class="COperation" field="sortie_salle" order_col=$order_col order_way=$order_way function=orderTabops}}</th>
    <th>{{mb_colonne class="COperation" field="entree_reveil" order_col=$order_col order_way=$order_way function=orderTabops}}</th>
    <th>Sortie sans SSPI</th>
    <th class="narrow"></th>
  </tr>    
  {{foreach from=$listOperations item=_operation}}
    {{assign var=patient value=$_operation->_ref_patient}}
    {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
    {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
    {{assign var=sejour_id value=$_operation->sejour_id}}
    <tr>
      <td>{{$_operation->_ref_salle->_shortview}}</td>

      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
      </td>

      <td class="text">
        <div style="float: right;">
          {{if $isImedsInstalled}}
            {{mb_include module=Imeds template=inc_sejour_labo link="#1" sejour=$_operation->_ref_sejour float="none"}}
          {{/if}}
        </div>

        <span class="CPatient-view {{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
              onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
          {{$_operation->_ref_patient->_view}}
        </span>
      </td>
      <td>
        <button class="button soins notext" onclick="showDossierSoins('{{$_operation->sejour_id}}','{{$_operation->_id}}');">Dossier de soin</button>
        {{if $isImedsInstalled}}
          <button class="labo button notext" onclick="showDossierSoins('{{$_operation->sejour_id}}','{{$_operation->_id}}','Imeds');">Labo</button>
        {{/if}}
        <button type="button" class="injection notext" onclick="Operation.dossierBloc('{{$_operation->_id}}', true)">Dossier de bloc</button>
        {{mb_include module=soins template=inc_antecedents_allergies patient_guid=$_operation->_ref_patient->_guid show_atcd=0}}

      </td>
      {{if $use_poste}}
        <td>
          {{mb_include module=dPsalleOp template=inc_form_toggle_poste_sspi type="ops"}}
        </td>
      {{/if}}
      {{if $isbloodSalvageInstalled}}
        {{assign var=salvage value=$_operation->_ref_blood_salvage}}
        <td>
          {{if $salvage->_id}}
          <div style="float:left ; display:inline">
            <a href="#" title="Voir la procédure RSPO" onclick="viewRSPO({{$_operation->_id}});">
            <img src="images/icons/search.png" title="Voir la procédure RSPO" alt="vw_rspo">
            {{if $salvage->_totaltime > "00:00:00"}}
              Débuté à {{$salvage->_recuperation_start|date_format:$conf.time}}
            {{else}}
              Non débuté
            {{/if}}
          </a>
          </div>
          {{if $salvage->_totaltime|date_format:$conf.time > "05:00"}}
          <div style="float:right; display:inline">

          <img src="images/icons/warning.png" title="Durée légale bientôt atteinte !" alt="alerte-durée-RSPO">
          {{/if}}
          </div>
          {{else}}
            Non inscrit
          {{/if}}
        </td>
      {{/if}}
      <td>
        {{if $can->edit}}
        <form name="editSortieBlocOpsFrm{{$_operation->_id}}" action="?" method="post">
          {{assign var=_operation_id value=$_operation->_id}}
          <input type="hidden" name="m" value="planningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          {{mb_key object=$_operation}}
          <input type="hidden" name="del" value="0" />
          {{mb_field object=$_operation field="sortie_salle" register=true form="editSortieBlocOpsFrm$_operation_id"}}
          <button class="tick notext" type="button" onclick="onSubmitFormAjax(this.form, refreshTabsReveil)">{{tr}}Modify{{/tr}}</button>
        </form>
        {{else}}
        {{mb_value object=$_operation field="sortie_salle"}}
        {{/if}}
      </td>
      <td>
        {{if $modif_operation}}

        {{if $personnels !== null}}
        <form name="selPersonnel{{$_operation->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="dPpersonnel" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="object_id" value="{{$_operation->_id}}" />
          <input type="hidden" name="object_class" value="{{$_operation->_class}}" />
          <input type="hidden" name="tag" value="reveil" />
          <input type="hidden" name="realise" value="0" />
          <select name="personnel_id" style="max-width: 120px;">
            <option value="">&mdash; Personnel</option>
            {{foreach from=$personnels item="personnel"}}
            <option value="{{$personnel->_id}}">{{$personnel->_ref_user}}</option>
            {{/foreach}}
          </select>
        </form>
        {{/if}}

        <form name="editEntreeReveilOpsFrm{{$_operation->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="planningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          {{mb_key object=$_operation}}
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="entree_reveil" value="" />
          <button class="tick notext" type="button" onclick="checkPersonnel(getForm('selPersonnel{{$_operation->_id}}'), this.form);">{{tr}}Modify{{/tr}}</button>
        </form>

        {{foreach from=$_operation->_ref_affectations_personnel.reveil item=curr_affectation}}
          <br />
          <form name="delPersonnel{{$curr_affectation->_id}}" action="?" method="post">
            <input type="hidden" name="m" value="personnel" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            <input type="hidden" name="del" value="1" />
            {{mb_key object=$curr_affectation}}
            <button type="button" class="trash notext" onclick="onSubmitFormAjax(this.form, refreshTabsReveil)">
              {{tr}}Delete{{/tr}}
            </button>
          </form>
          {{$curr_affectation->_ref_personnel->_ref_user}}
        {{/foreach}}
        {{else}}
          -
        {{/if}}

        {{mb_include module=forms template=inc_widget_ex_class_register_multiple object=$_operation cssStyle="display: inline-block;"}}
      </td>
      <td class="button">
        {{if $modif_operation}}
        <form name="editSortieReveilOpsFrm{{$_operation->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="planningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="entree_reveil" value="current" />
          <input type="hidden" name="sortie_reveil_possible" value="current" />
          <input type="hidden" name="sortie_reveil_reel" value="current" />
          <button class="tick notext" type="button" onclick="onSubmitFormAjax(this.form, refreshTabsReveil)">
            {{tr}}Modify{{/tr}}
          </button>
        </form>
        {{else}}-{{/if}}
      </td>
      <td>
        <button type="button" class="print notext"
          onclick="printDossier('{{$_operation->sejour_id}}', '{{$_operation->_id}}')"></button>
      </td>
    </tr>
  {{foreachelse}}
    <tr><td colspan="20" class="empty">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>

{{mb_include module=forms template=inc_widget_ex_class_register_multiple_end event_name=entree_reveil object_class="COperation"}}