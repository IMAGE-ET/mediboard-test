<script type="text/javascript">

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
      <a href="?m={{$m}}&amp;tab=vw_soins_reveil&amp;operation_id={{$_operation->_id}}">
      <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
        {{$_operation->_ref_patient->_view}}
      </span>
      </a>
    </td>
    <td class="text">
      {{assign var="affectation" value=$_operation->_ref_sejour->_ref_first_affectation}}
      {{if $affectation->affectation_id}}
      {{$affectation->_ref_lit->_view}}
      {{else}}
      Non placé
      {{/if}}
    </td>
    <td class="button">
      {{if $can->edit}}
      <form name="editSortieBlocFrm{{$_operation->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{assign var=_operation_id value=$_operation->_id}}
        {{mb_field object=$_operation field=sortie_salle form="editSortieBlocFrm$_operation_id"}}
     <button class="tick notext" type="button" onclick="submitSortieForm(this.form);">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}
      {{mb_value object=$_operation field="sortie_salle}}
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
        {{mb_field object=$_operation field=entree_reveil form="editEntreeReveilFrm$_operation_id"}}
        <button class="tick notext" type="button" onclick="submitSortieForm(this.form);">{{tr}}Modify{{/tr}}</button>
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
        {{if $can->edit}}
        {{mb_field object=$_operation field=sortie_reveil form="editSortieReveilFrm$_operation_id"}}
        <button class="tick notext" type="button" onclick="submitSortieForm(this.form);">{{tr}}Modify{{/tr}}</button>

        <button class="cancel notext" type="button" onclick="$V(this.form.sortie_reveil, ''); submitSortieForm(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="sortie_reveil" onchange="submitSortieForm(this.form);">
          <option value="">-</option>
          {{foreach from=$timing.$key.sortie_reveil|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $_operation->sortie_reveil}}selected="selected"{{/if}}>
            {{$curr_time|date_format:$dPconfig.time}}
          </option>
          {{/foreach}}
        </select>
        <button class="cancel notext" type="button" onclick="$V(this.form.sortie_reveil, ''); submitSortieForm(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{mb_value object=$_operation field="sortie_reveil"}}
        {{/if}}
      </form>
    </td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="20">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>

<script type="text/javascript">
  $('liout').innerHTML = {{$listOperations|@count}};
</script>