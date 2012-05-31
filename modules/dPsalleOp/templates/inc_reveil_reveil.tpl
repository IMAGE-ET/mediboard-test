<script type="text/javascript">
  Main.add(function () {    
    Control.Tabs.setTabCount("reveil", "{{$listOperations|@count}}");
    
    {{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}
  });

  submitReveilForm = function(oFormOperation) {
    submitFormAjax(oFormOperation,'systemMsg', {onComplete: function(){refreshTabsReveil()}});
  }
</script> 

<table class="tbl">
  <tr>
    <th>{{tr}}SSPI.Salle{{/tr}}</th>
    <th>{{tr}}SSPI.Praticien{{/tr}}</th>
    <th>{{tr}}SSPI.Patient{{/tr}}</th>
    <th>{{tr}}SSPI.Chambre{{/tr}}</th>    
    {{if $isbloodSalvageInstalled}}
      <th>{{tr}}SSPI.RSPO{{/tr}}</th>
    {{/if}}
    {{if $personnels !== null}}
    <th>{{tr}}SSPI.SortieSalle{{/tr}}</th>
    {{/if}}
    <th>{{tr}}SSPI.Responsable{{/tr}}</th>
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
        
        <a href="#" style="display: inline" onclick="codageCCAM('{{$_operation->_id}}');">
          <img src="images/icons/anesth.png" alt="Anesth" />
        </a>
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
    {{if $isbloodSalvageInstalled}}
	    <td>
	      {{if $_operation->blood_salvage->_id}}
	      <div style="float:left ; display:inline">
	        <a href="#" title="Voir la procédure RSPO" onclick="viewRSPO({{$_operation->_id}});">         
	        <img src="images/icons/search.png" title="Voir la procédure RSPO" alt="vw_rspo" />
	        {{if $_operation->blood_salvage->totaltime > "00:00:00"}}  
	         Débuté à {{$_operation->blood_salvage->_recuperation_start|date_format:$conf.time}}
	        {{else}}
	          Non débuté
	        {{/if}} 
	      </a>
	      </div>
	      {{if $_operation->blood_salvage->totaltime|date_format:$conf.time > "05:00"}} 
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
        <form name="editSortieBlocFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
          <input type="hidden" name="del" value="0" />
          {{mb_field object=$_operation field="sortie_salle" register=true form="editSortieBlocFrm$_operation_id"}}
          <button class="tick notext" type="button" onclick="submitReveilForm(this.form);">{{tr}}Modify{{/tr}}</button>
        </form>
      {{else}}
      {{mb_value object=$_operation field="sortie_salle"}}
      {{/if}}
    </td>
    {{if $personnels !== null}}
    <td>
      <form name="selPersonnel{{$_operation->_id}}" action="?m={{$m}}" method="post">
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
        <option value="{{$personnel->_id}}">{{$personnel->_ref_user->_view}}</option>
        {{/foreach}}
        </select>
        <button type="button" class="add notext" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: function() { refreshTabsReveil(); }})">
          {{tr}}Add{{/tr}}
        </button>
      </form>
      {{foreach from=$_operation->_ref_affectations_personnel.reveil item=curr_affectation}}
        <br />
        <form name="delPersonnel{{$curr_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPpersonnel" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="affect_id" value="{{$curr_affectation->_id}}" />
          <button type="button" class="trash notext" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: function() { refreshTabsReveil(); }})">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
        {{$curr_affectation->_ref_personnel->_ref_user->_view}}
      {{/foreach}}
    </td>
    {{/if}}
    <td>
      <form name="editEntreeReveilFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{if $_operation->_ref_sejour->type=="exte"}}
        -
        {{elseif $modif_operation}}
        {{mb_field object=$_operation field="entree_reveil" form="editEntreeReveilFrm$_operation_id" onchange="submitReveilForm(this.form);"}}
        {{else}}
          {{mb_value object=$_operation field="entree_reveil"}}
        {{/if}}
      </form>
    </td>
    <td class="button">
      {{if $modif_operation}}
      <form name="editSortieReveilFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="sortie_reveil" value="" />
        <button class="tick notext" type="button" onclick="$V(this.form.sortie_reveil, 'current') ; submitReveilForm(this.form);">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}-{{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="20" class="empty">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>
