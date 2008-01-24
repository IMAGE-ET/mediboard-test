<table class="form">
  <tr>
    <th class="category">{{$listOut|@count}} patient(s) sortis du bloc</th>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>Salle</th>
    <th>Praticien</th>
    <th>Patient</th>
    <th>Chambre</th>
    <th>Sortie Salle</th>
    <th>Entrée reveil</th>
    <th>Sortie reveil</th>
  </tr> 
  {{foreach from=$listOut key=key item=curr_op}}
  <tr>
    <td>{{$curr_op->_ref_salle->nom}}</td>
    <td class="text">Dr. {{$curr_op->_ref_chir->_view}}</td>
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
     <input name="sortie_salle" size="3" maxlength="5" type="text" value="{{$curr_op->sortie_salle|date_format:"%H:%M"}}">
     <button class="tick notext" type="submit">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}
      {{$curr_op->sortie_salle|date_format:"%Hh%M"}}
      {{/if}}
    </td>
    <td class="button">
      {{if $can->edit}}
      <form name="editEntreeReveilFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <input type="hidden" name="del" value="0" />
        <input name="entree_reveil" size="3" maxlength="5" type="text" value="{{$curr_op->entree_reveil|date_format:"%H:%M"}}">
        <button class="tick notext" type="submit">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}
      {{$curr_op->entree_reveil|date_format:"%Hh%M"}}
      {{/if}}
      
      {{if $curr_op->_ref_affectation_reveil->_id}}
      <br />{{$curr_op->_ref_affectation_reveil->_ref_personnel->_ref_user->_view}}
      {{/if}}
      
    </td>
    <td class="button">
      <form name="editSortieReveilFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{if $can->edit}}
        <input name="sortie_reveil" size="3" maxlength="5" type="text" value="{{$curr_op->sortie_reveil|date_format:"%H:%M"}}">
        <button class="tick notext" type="submit">{{tr}}Modify{{/tr}}</button>

        <button class="cancel notext" type="submit" onclick="this.form.sortie_reveil.value = ''">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="sortie_reveil" onchange="this.form.submit()">
          <option value="">-</option>
          {{foreach from=$timing.$key.sortie_reveil|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $curr_op->sortie_reveil}}selected="selected"{{/if}}>
            {{$curr_time|date_format:"%Hh%M"}}
          </option>
          {{/foreach}}
        </select>
        <button class="cancel notext" type="submit" onclick="this.form.sortie_reveil.value = ''">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{$curr_op->sortie_reveil|date_format:"%Hh%M"}}
        {{/if}}
      </form>
    </td>
  </tr>
  {{/foreach}}
</table>