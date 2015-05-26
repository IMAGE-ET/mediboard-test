{{assign var=use_poste value=$conf.dPplanningOp.COperation.use_poste}}
{{assign var=use_sortie_reveil_reel value="dPsalleOp COperation use_sortie_reveil_reel"|conf:"CGroups-$g"}}
{{assign var=password_sortie value="dPsalleOp COperation password_sortie"|conf:"CGroups-$g"}}

{{if $require_check_list && !"dPsalleOp CDailyCheckList choose_moment_edit"|conf:"CGroups-$g"}}
  <table class="main layout">
    <tr>
      {{foreach from=$daily_check_lists item=check_list}}
        <td>
          <h2>{{$check_list->_ref_list_type->title}}</h2>
          {{if $check_list->_ref_list_type->description}}
            <p>{{$check_list->_ref_list_type->description}}</p>
          {{/if}}

          {{mb_include module=salleOp template=inc_edit_check_list
          check_list=$check_list
          check_item_categories=$check_list->_ref_list_type->_ref_categories
          list_chirs=$listChirs
          list_anesths=$listAnesths
          personnel=$personnels}}
        </td>
      {{/foreach}}
    </tr>
  </table>
  {{mb_return}}
{{/if}}

{{if $require_check_list_close}}
  {{mb_include module=salleOp template=inc_last_valid_checklist date_checklist=$date_close_checklist object_id=$bloc_id type='fermeture_sspi'}}
{{/if}}
{{if "dPsalleOp CDailyCheckList choose_moment_edit"|conf:"CGroups-$g" && $require_check_list}}
  {{mb_include module=salleOp template=inc_last_valid_checklist date_checklist=$date_open_checklist object_id=$bloc_id type='ouverture_sspi'}}
{{/if}}

<script>
  Main.add(function() {
    Control.Tabs.setTabCount("reveil", "{{$listOperations|@count}}");

    {{if $isImedsInstalled}}
    ImedsResultsWatcher.loadResults();
    {{/if}}
  });

  submitReveilForm = function(oFormOperation) {
    onSubmitFormAjax(oFormOperation, refreshTabsReveil);
  };

  submitReveil = function(form) {
    {{if $password_sortie && (!$is_anesth || !$app->user_prefs.autosigne_sortie)}}
      window.current_form = form;
      var url = new Url("salleOp", "ajax_lock_sortie");
      url.requestModal("30%", "20%", {
        onClose: function() {
          $V(form.sortie_reveil_possible_da, '', false);
          $V(form.sortie_reveil_possible, '', false);
        },
        onComplete: function() {
          // Pré-selection si anesthésiste dans la modale de saisie du mot de passe
          {{if $is_anesth}}
          var form_sortie = getForm("lock_sortie");
          $V(form_sortie.user_id, '{{$app->user_id}}');
          {{/if}}
        }
      });
    {{else}}
      $V(form.sortie_locker_id, '{{$app->user_id}}');
      submitReveilForm(form);
    {{/if}}
  };

  orderTabreveil = function(col, way) {
    orderTabReveil(col, way, 'reveil');
  };
</script>

<table class="tbl">
  <tr>
    <th>{{mb_colonne class="COperation" field="salle_id" order_col=$order_col order_way=$order_way function=orderTabreveil}}</th>
    <th>{{mb_colonne class="COperation" field="chir_id" order_col=$order_col order_way=$order_way function=orderTabreveil}}</th>
    <th>{{mb_colonne class="COperation" field="_patient" order_col=$order_col order_way=$order_way function=orderTabreveil}} <input type="text" name="_seek_patient_preop" value="" class="seek_patient" onkeyup="seekPatient(this);" onchange="seekPatient(this);" /></th>
    <th class="narrow">Dossier</th>
    {{if $use_poste}}
      <th>{{mb_colonne class="COperation" field="poste_sspi_id" order_col=$order_col order_way=$order_way function=orderTabreveil}}</th>
    {{/if}}
    <th>{{tr}}SSPI.Chambre{{/tr}}</th>    
    {{if $isbloodSalvageInstalled}}
      <th>{{tr}}SSPI.RSPO{{/tr}}</th>
    {{/if}}
    {{if $personnels !== null}}
    <th>{{mb_colonne class="COperation" field="sortie_salle" order_col=$order_col order_way=$order_way function=orderTabreveil}}</th>
    {{/if}}
    <th>{{tr}}SSPI.Responsable{{/tr}}</th>
    <th>{{mb_colonne class="COperation" field="entree_reveil" order_col=$order_col order_way=$order_way function=orderTabreveil}}</th>
    {{if @$modules.brancardage->_can->read}}
      <th>{{tr}}CBrancardage{{/tr}}</th>
    {{/if}}
    {{if $use_sortie_reveil_reel}}
      <th>{{mb_colonne class="COperation" field="sortie_reveil_possible" order_col=$order_col order_way=$order_way function=orderTabreveil}}</th>
      <th style="width: 15%">{{mb_colonne class="COperation" field="sortie_reveil_reel" order_col=$order_col order_way=$order_way function=orderTabreveil}}</th>
    {{else}}
      <th>{{mb_colonne class="COperation" field="sortie_reveil_reel" order_col=$order_col order_way=$order_way function=orderTabreveil}}</th>
    {{/if}}
    <th class="narrow"></th>
  </tr>    
  {{foreach from=$listOperations key=key item=_operation}}
    {{assign var=patient value=$_operation->_ref_patient}}
    {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
    {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
    {{assign var=sejour_id value=$_operation->sejour_id}}
    {{assign var=_operation_id value=$_operation->_id}}
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
      {{$_operation->_ref_patient}}
      </span>
    </td>
    <td>
      <button class="soins button notext" onclick="showDossierSoins('{{$_operation->sejour_id}}','{{$_operation->_id}}');">Dossier de soins</button>
      {{if $isImedsInstalled}}
        <button class="labo button notext" onclick="showDossierSoins('{{$_operation->sejour_id}}','{{$_operation->_id}}','Imeds');">Labo</button>
      {{/if}}
      <button type="button" class="injection notext" onclick="Operation.dossierBloc('{{$_operation->_id}}', true)">Dossier de bloc</button>
      {{mb_include module=soins template=inc_antecedents_allergies patient_guid=$_operation->_ref_patient->_guid show_atcd=0}}
    </td>
    {{if $use_poste}}
      <td>
        {{mb_include module=dPsalleOp template=inc_form_toggle_poste_sspi type="reveil"}}
      </td>
    {{/if}}
    <td class="text">
      {{mb_include module=hospi template=inc_placement_sejour sejour=$_operation->_ref_sejour}}
    </td>
    {{if $isbloodSalvageInstalled}}
      {{assign var=salvage value=$_operation->_ref_blood_salvage}}
      <td>
        {{if $salvage->_id}}
        <div style="float:left ; display:inline">
          <a href="#" title="Voir la procédure RSPO" onclick="viewRSPO({{$_operation->_id}});">
          <img src="images/icons/search.png" title="Voir la procédure RSPO" alt="vw_rspo" />
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
      {{mb_value object=$_operation field="sortie_salle"}}
    </td>
    {{if $personnels !== null}}
    <td>
      <form name="selPersonnel{{$_operation->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="personnel" />
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
        <button type="button" class="add notext" onclick="onSubmitFormAjax(this.form, refreshTabsReveil)">
          {{tr}}Add{{/tr}}
        </button>
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
    </td>
    {{/if}}
    <td>
      <form name="editEntreeReveilReveilFrm{{$_operation->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="planningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$_operation}}
        <input type="hidden" name="del" value="0" />
        {{if $_operation->_ref_sejour->type=="exte"}}
        -
        {{elseif $modif_operation && !$_operation->sortie_reveil_possible}}
          {{mb_field object=$_operation field="entree_reveil" form="editEntreeReveilReveilFrm$_operation_id" onchange="submitReveilForm(this.form);"}}
        {{else}}
          {{mb_value object=$_operation field="entree_reveil"}}
        {{/if}}
      </form>
    </td>
    {{if @$modules.brancardage->_can->read}}
      <td>
         <span id="demande_brancard-{{$_operation->sejour_id}}">
           {{mb_include module=brancardage template=inc_exist_brancard colonne="demande_brancard" reveil=true origine="CBlocOperatoire" origine_guid="CBlocOperatoire-$bloc_id"}}
         </span>
      </td>
    {{/if}}
    <td class="button">
      {{if $modif_operation}}
      <form name="editSortieReveilReveilFrm{{$_operation->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="planningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$_operation}}
        <input type="hidden" name="del" value="0" />
        {{mb_field object=$_operation field=sortie_locker_id hidden=1}}
        {{if $_operation->sortie_locker_id}}
          <span onmouseover="ObjectTooltip.createDOM(this, 'info_locker_{{$_operation_id}}')">
                {{mb_field object=$_operation field="sortie_reveil_possible" hidden=1}}
            {{mb_value object=$_operation field="sortie_reveil_possible"}}
            <button type="button" class="cancel notext" title="Annuler la validation"
                    onclick="$V(this.form.sortie_reveil_possible, ''); $V(this.form.sortie_reveil_reel, ''); $V(this.form.sortie_locker_id, ''); submitSortie(this.form);"></button>
              </span>
          <div id="info_locker_{{$_operation_id}}" style="display: none">
            Validée par {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_sortie_locker}}
          </div>
        {{else}}
          {{mb_field object=$_operation field=sortie_reveil_possible form=editSortieReveilReveilFrm`$_operation->_id` onchange="submitReveil(this.form)"}}
        {{/if}}
        {{if !$_operation->sortie_reveil_possible}}
          <button class="tick notext" type="button"
            onclick="if (!this.form.sortie_reveil_possible.value) { $V(this.form.sortie_reveil_possible, 'current', false); }; submitReveil(this.form);">{{tr}}Modify{{/tr}}</button>
        {{/if}}
      </form>
      {{else}}-{{/if}}
      
      {{mb_include module=forms template=inc_widget_ex_class_register_multiple object=$_operation cssStyle="display: inline-block;"}}
    </td>
    {{if $use_sortie_reveil_reel}}
      <td class="button">
        <form name="editSortieReveilReelReveilFrm{{$_operation->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="planningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          {{mb_key object=$_operation}}
          <input type="hidden" name="del" value="0" />
          {{if $modif_operation}}
            {{mb_field object=$_operation field=sortie_reveil_reel register=true form="editSortieReveilReelReveilFrm$_operation_id" onchange="submitReveilForm(this.form);"}}
            {{if !$_operation->sortie_reveil_reel}}
              <button class="tick notext" type="button"
              onclick="if (!this.form.sortie_reveil_reel.value) { $V(this.form.sortie_reveil_reel, 'current'); }; submitReveilForm(this.form);">{{tr}}Modify{{/tr}}</button>
            {{/if}}
          {{else}}
            {{mb_value object=$_operation field="sortie_reveil_reel"}}
          {{/if}}
        </form>
        </td>
    {{/if}}
    <td>
      <button type="button" class="print notext"
        onclick="printDossier('{{$_operation->sejour_id}}', '{{$_operation->_id}}')"></button>
    </td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="20" class="empty">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>

{{mb_include module=forms template=inc_widget_ex_class_register_multiple_end event_name=sortie_reveil object_class="COperation"}}