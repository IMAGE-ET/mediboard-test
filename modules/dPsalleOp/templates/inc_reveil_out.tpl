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

<table class="tbl">
  <tr>
    <th>{{tr}}SSPI.Salle{{/tr}}</th>
    <th>{{tr}}SSPI.Praticien{{/tr}}</th>
    <th>{{tr}}SSPI.Patient{{/tr}}</th>
    <th>{{tr}}SSPI.Chambre{{/tr}}</th>
    <th>{{tr}}SSPI.SortieSalle{{/tr}}</th>
    <th>{{tr}}SSPI.EntreeReveil{{/tr}}</th>
    <th>{{tr}}SSPI.SortieReveil{{/tr}}</th>
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
    
    <td class="text">
      {{mb_include module=hospi template=inc_placement_sejour sejour=$_operation->_ref_sejour}}
    </td>
    
    <td class="button">
      {{if $can->edit}}
      <form name="editSortieBlocFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{assign var=_operation_id value=$_operation->_id}}
        {{mb_field object=$_operation field=sortie_salle form="editSortieBlocFrm$_operation_id" onchange="submitSortieForm(this.form);"}}
      </form>
      {{else}}
        {{mb_value object=$_operation field="sortie_salle"}}
      {{/if}}
    </td>
    <td class="button">
      {{if $_operation->entree_reveil}}
        {{if $can->edit}}
        <form name="editEntreeReveilFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
          <input type="hidden" name="del" value="0" />
          {{mb_field object=$_operation field=entree_reveil form="editEntreeReveilFrm$_operation_id" onchange="submitSortieForm(this.form);"}}
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
      <form name="editSortieReveilFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{if $modif_operation}}
          {{mb_field object=$_operation field=sortie_reveil register=true form="editSortieReveilFrm$_operation_id" onchange="submitSortieForm(this.form);"}}
        {{else}}
          {{mb_value object=$_operation field="sortie_reveil"}}
        {{/if}}
      </form>
      
      {{mb_include module=forms template=inc_widget_ex_class_register object=$_operation event=sortie_reveil cssStyle="display: inline-block; font-size: 0.8em;"}}
    </td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="20" class="empty">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>
