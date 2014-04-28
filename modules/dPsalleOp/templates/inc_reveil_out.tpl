{{assign var=use_sortie_reveil_reel value="dPsalleOp COperation use_sortie_reveil_reel"|conf:"CGroups-$g"}}
{{assign var=use_poste value=$conf.dPplanningOp.COperation.use_poste}}
{{assign var=password_sortie value="dPsalleOp COperation password_sortie"|conf:"CGroups-$g"}}

<script>
  Main.add(function () {
    {{if $use_sortie_reveil_reel}}
      Control.Tabs.setTabCount("out", "{{$nb_sorties_non_realisees}}", "{{$listOperations|@count}}");
    {{else}}
      Control.Tabs.setTabCount("out", "{{$listOperations|@count}}");
    {{/if}}
    {{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}
  });

  submitSortieForm = function(oFormSortie) {
    onSubmitFormAjax(oFormSortie, refreshTabsReveil);
  }

  submitSortie = function(form) {
    {{if $password_sortie && (!$is_anesth || !$app->user_prefs.autosigne_sortie)}}
      window.current_form = form;
      var url = new Url("salleOp", "ajax_lock_sortie");
      url.requestModal("30%", "20%", {onComplete: function() {
        {{if $is_anesth}}
        var form_sortie = getForm("lock_sortie");
        $V(form_sortie.user_id, '{{$app->user_id}}');
        {{/if}}
      }});
    {{else}}
      submitSortieForm(form);
    {{/if}}
  }
</script>

<table class="tbl">
  <tr>
    <th>{{tr}}SSPI.Salle{{/tr}}</th>
    <th>{{tr}}SSPI.Praticien{{/tr}}</th>
    <th>{{tr}}SSPI.Patient{{/tr}}</th>
    <th class="narrow"></th>
    {{if $use_poste}}
      <th>{{tr}}SSPI.Poste{{/tr}}</th>
    {{/if}}
    <th>{{tr}}SSPI.Chambre{{/tr}}</th>
    <th>{{tr}}SSPI.SortieSalle{{/tr}}</th>
    <th>{{tr}}SSPI.EntreeReveil{{/tr}}</th>
    {{if $use_sortie_reveil_reel}}
      <th style="width: 15%">{{tr}}SSPI.SortieReveilPossible{{/tr}}</th>
      <th style="width: 15%">{{tr}}SSPI.SortieReveilReel{{/tr}}</th>
    {{else}}
      <th style="width: 15%">{{tr}}SSPI.SortieReveil{{/tr}}</th>
    {{/if}}
    <th class="narrow"></th>
  </tr> 
  {{foreach from=$listOperations key=key item=_operation}}
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
      
        <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
        {{$_operation->_ref_patient}}
      </span>
    </td>
    <td>
      <button class="notext button soins" onclick="showDossierSoins('{{$_operation->sejour_id}}','{{$_operation->_id}}');">Dossier de soins</button>
      <button type="button" class="injection notext" onclick="Operation.dossierBloc('{{$_operation->_id}}', true)">Dossier de bloc</button>
    </td>

    {{if $use_poste}}
      <td>
        {{mb_include module=dPsalleOp template=inc_form_toggle_poste_sspi type="out"}}
      </td>
    {{/if}}

    <td class="text">
      {{mb_include module=hospi template=inc_placement_sejour sejour=$_operation->_ref_sejour}}
    </td>
    
    <td class="button">
      {{if $can->edit}}
      <form name="editSortieBlocOutFrm{{$_operation->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="planningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$_operation}}
        <input type="hidden" name="del" value="0" />
        {{mb_field object=$_operation field=sortie_salle form="editSortieBlocOutFrm$_operation_id" onchange="submitSortieForm(this.form);"}}
      </form>
      {{else}}
        {{mb_value object=$_operation field="sortie_salle"}}
      {{/if}}
    </td>
    <td class="button">
      {{if $_operation->entree_reveil}}
        {{if $can->edit && !$_operation->sortie_reveil_possible}}
        <form name="editEntreeReveilOutFrm{{$_operation->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="planningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          {{mb_key object=$_operation}}
          <input type="hidden" name="del" value="0" />
          {{mb_field object=$_operation field=entree_reveil form="editEntreeReveilOutFrm$_operation_id" onchange="submitSortieForm(this.form);"}}
        </form>
        {{else}}
          {{mb_value object=$_operation field="entree_reveil"}}
        {{/if}}
      {{else}}
        pas de passage SSPI
      {{/if}}
      
      {{foreach from=$_operation->_ref_affectations_personnel.reveil item=curr_affectation}}
        <br />
        {{$curr_affectation->_ref_personnel->_ref_user}}
      {{/foreach}}
    </td>
    <td class="button">
      <form name="editSortieReveilOutFrm{{$_operation->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="planningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$_operation}}
        <input type="hidden" name="del" value="0" />
        {{mb_field object=$_operation field="entree_reveil" hidden=1}}
        {{mb_field object=$_operation field="sortie_reveil_reel" hidden=1}}
        {{mb_field object=$_operation field="sortie_locker_id" hidden=1}}
        {{if $modif_operation && !$_operation->sortie_locker_id}}
          {{mb_field object=$_operation field=sortie_reveil_possible register=true form="editSortieReveilOutFrm$_operation_id"
            onchange="if (!this.value && !this.form.entree_reveil.value) { \$V(this.form.sortie_reveil_reel, '') } submitSortie(this.form);"}}
        {{else}}
          {{if $_operation->sortie_locker_id && !$use_sortie_reveil_reel}}
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
            {{mb_value object=$_operation field="sortie_reveil_possible"}}
          {{/if}}
        {{/if}}
      </form>      
    </td>
    {{if $use_sortie_reveil_reel}}
      <td class="button">
        <form name="editSortieReveilReelOutFrm{{$_operation->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="planningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          {{mb_key object=$_operation}}
          <input type="hidden" name="del" value="0" />
          {{mb_field object=$_operation field="entree_reveil" hidden=1}}
          {{mb_field object=$_operation field="sortie_reveil_possible" hidden=1}}
          {{if $modif_operation}}
            {{mb_field object=$_operation field=sortie_reveil_reel register=true form="editSortieReveilReelOutFrm$_operation_id"
              onchange="if (!this.value && !this.form.entree_reveil.value) { \$V(this.form.sortie_reveil_possible, ''); } submitSortieForm(this.form);"}}
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
