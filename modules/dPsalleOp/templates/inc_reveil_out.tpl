<script type="text/javascript">

submitSortieForm = function(oFormSortie) {
  submitFormAjax(oFormSortie,'systemMsg', {onComplete: function(){ refreshOutPanels() }});
}

function refreshOutPanels() {
  var url = new Url;
      
  url.setModuleAction("dPsalleOp", "httpreq_reveil_reveil");
  url.addParam('date',"{{$date}}");
  url.requestUpdate("reveil", {waitingText : null});

  url.setModuleAction("dPsalleOp", "httpreq_reveil_ops");
  url.addParam('date',"{{$date}}");
  url.requestUpdate("ops", {waitingText : null});
  
  url.setModuleAction("dPsalleOp", "httpreq_reveil_out");
  url.addParam('date',"{{$date}}");
  url.requestUpdate("out", {waitingText : null});
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
  {{foreach from=$listOut key=key item=curr_op}}
  <tr>
    <td>{{$curr_op->_ref_salle->nom}}</td>
    <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
    <td class="text">{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
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
        {{mb_field object=$curr_op field=sortie_salle}}
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
        {{mb_field object=$curr_op field=entree_reveil}}
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
        {{mb_field object=$curr_op field=sortie_reveil}}
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
  $('liout').innerHTML = {{$listOut|@count}};
</script>