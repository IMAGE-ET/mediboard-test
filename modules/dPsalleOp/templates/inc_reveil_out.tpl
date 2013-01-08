<script type="text/javascript">  
  Main.add(function () {
    Control.Tabs.setTabCount("out", "{{$listOperations|@count}}");
    
    {{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}
  });

  submitSortieForm = function(oFormSortie) {
    submitFormAjax(oFormSortie,'systemMsg', {onComplete: function(){ refreshTabsReveil() }});
  }
</script>

{{assign var=use_sortie_reveil_reel value=$conf.dPsalleOp.COperation.use_sortie_reveil_reel}}
{{assign var=use_poste value=$conf.dPplanningOp.COperation.use_poste}}

<table class="tbl">
  <tr>
    <th>{{tr}}SSPI.Salle{{/tr}}</th>
    <th>{{tr}}SSPI.Praticien{{/tr}}</th>
    <th>{{tr}}SSPI.Patient{{/tr}}</th>
    {{if $use_poste}}
      <th>{{tr}}SSPI.Poste{{/tr}}</th>
    {{/if}}
    <th>{{tr}}SSPI.Chambre{{/tr}}</th>
    <th>{{tr}}SSPI.SortieSalle{{/tr}}</th>
    <th>{{tr}}SSPI.EntreeReveil{{/tr}}</th>
    <th>{{tr}}SSPI.SortieReveil{{/tr}}</th>
    {{if $use_sortie_reveil_reel}}
      <th>{{tr}}SSPI.SortieReveilReel{{/tr}}</th>
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
      
      <a href="#" onclick="showDossierSoins('{{$_operation->sejour_id}}','{{$_operation->_id}}');">
        <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
        {{$_operation->_ref_patient->_view}}
      </span>
      </a>
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
      <form name="editSortieBlocOutFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{assign var=_operation_id value=$_operation->_id}}
        {{mb_field object=$_operation field=sortie_salle form="editSortieBlocOutFrm$_operation_id" onchange="submitSortieForm(this.form);"}}
      </form>
      {{else}}
        {{mb_value object=$_operation field="sortie_salle"}}
      {{/if}}
    </td>
    <td class="button">
      {{if $_operation->entree_reveil}}
        {{if $can->edit}}
        <form name="editEntreeReveilOutFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
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
        {{$curr_affectation->_ref_personnel->_ref_user->_view}}
      {{/foreach}}
    </td>
    <td class="button">
      <form name="editSortieReveilOutFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{if $modif_operation}}
          {{mb_field object=$_operation field=sortie_reveil_possible register=true form="editSortieReveilOutFrm$_operation_id" onchange="submitSortieForm(this.form);"}}
        {{else}}
          {{mb_value object=$_operation field="sortie_reveil_possible"}}
        {{/if}}
      </form>      
    </td>
    {{if $use_sortie_reveil_reel}}
      <td class="button">
        <form name="editSortieReveilReelOutFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
          <input type="hidden" name="del" value="0" />
          {{if $modif_operation}}
            {{mb_field object=$_operation field=sortie_reveil_reel register=true form="editSortieReveilReelOutFrm$_operation_id" onchange="submitSortieForm(this.form);"}}
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
