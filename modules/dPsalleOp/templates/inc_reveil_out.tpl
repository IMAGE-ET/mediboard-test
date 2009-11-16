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
  {{foreach from=$listOperations key=key item=curr_op}}
	{{assign var=operation_id value=$curr_op->_id}}
  <tr>
    <td>{{$curr_op->_ref_salle->nom}}</td>
    <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
    <td class="text">
    	<a href="?m={{$m}}&amp;tab=vw_soins_reveil&amp;operation_id={{$curr_op->_id}}" title="Soins">
			{{$curr_op->_ref_sejour->_ref_patient->_view}}
			</a>
	  </td>
    <td class="text">
      {{assign var="affectation" value=$curr_op->_ref_sejour->_ref_first_affectation}}
      {{if $affectation->affectation_id}}
      {{$affectation->_ref_lit->_view}}
      {{else}}
      Non placé
      {{/if}}
    </td>
    <td class="button">
      {{if $can->edit}}
      <form name="editSortieBlocFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{assign var=curr_op_id value=$curr_op->_id}}
        {{mb_field object=$curr_op field=sortie_salle form="editSortieBlocFrm$operation_id"}}
     <button class="tick notext" type="button" onclick="submitSortieForm(this.form);">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}
      {{mb_value object=$curr_op field="sortie_salle}}
      {{/if}}
    </td>
    <td class="button">
      {{if $curr_op->entree_reveil}}
      {{if $can->edit}}
      <form name="editEntreeReveilFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{mb_field object=$curr_op field=entree_reveil form="editEntreeReveilFrm$operation_id"}}
        <button class="tick notext" type="button" onclick="submitSortieForm(this.form);">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}
      {{mb_value object=$curr_op field="entree_reveil"}}
      {{/if}}
      {{else}}
        pas de passage SSPI
      {{/if}}
      
      {{foreach from=$curr_op->_ref_affectations_personnel.reveil item=curr_affectation}}
        <br />
        {{$curr_affectation->_ref_personnel->_ref_user->_view}}
      {{/foreach}}
      
    </td>
    <td class="button">
      <form name="editSortieReveilFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{if $can->edit}}
        {{mb_field object=$curr_op field=sortie_reveil form="editSortieReveilFrm$operation_id"}}
        <button class="tick notext" type="button" onclick="submitSortieForm(this.form);">{{tr}}Modify{{/tr}}</button>

        <button class="cancel notext" type="button" onclick="$V(this.form.sortie_reveil, ''); submitSortieForm(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="sortie_reveil" onchange="submitSortieForm(this.form);">
          <option value="">-</option>
          {{foreach from=$timing.$key.sortie_reveil|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $curr_op->sortie_reveil}}selected="selected"{{/if}}>
            {{$curr_time|date_format:$dPconfig.time}}
          </option>
          {{/foreach}}
        </select>
        <button class="cancel notext" type="button" onclick="$V(this.form.sortie_reveil, ''); submitSortieForm(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{mb_value object=$curr_op field="sortie_reveil"}}
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