      <table class="form">
        <tr>
          <th class="category">{{$listReveil|@count}} patients en salle de reveil</th>
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
        {{foreach from=$listReveil key=key item=curr_op}}
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
            {{if $canEdit}}
              <form name="editSortieBlocFrm{{$curr_op->operation_id}}" action="index.php?m={{$m}}" method="post">
                <input type="hidden" name="m" value="dPplanningOp" />
                <input type="hidden" name="dosql" value="do_planning_aed" />
                <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
                <input type="hidden" name="del" value="0" />
	            <input name="sortie_bloc" size="5" type="text" value="{{$curr_op->sortie_bloc|date_format:"%H:%M"}}">
	            <button class="tick notext" type="submit"></button>
	          </form>
            {{else}}
            {{$curr_op->sortie_bloc|date_format:"%Hh%M"}}
            {{/if}}
          </td>
          <td class="button">
            <form name="editSortieBlocFrm{{$curr_op->operation_id}}" action="index.php?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
              <input type="hidden" name="del" value="0" />
              {{if $canEdit}}
	          <input name="entree_reveil" size="5" type="text" value="{{$curr_op->entree_reveil|date_format:"%H:%M"}}">
	          <button class="tick notext" type="submit"></button>
              {{else}}
              <select name="entree_reveil" onchange="this.form.submit()">
                <option value="">-</option>
                {{foreach from=$timing.$key.entree_reveil item=curr_time}}
                <option value="{{$curr_time}}" {{if $curr_time == $curr_op->entree_reveil}}selected="selected"{{/if}}>
                  {{$curr_time|date_format:"%Hh%M"}}
                </option>
                {{/foreach}}
              </select>
              {{/if}}
              <button class="cancel notext" type="submit" onclick="this.form.entree_reveil.value = ''"></button>
            </form>
          </td>
          <td class="button">
            <form name="editEntreeReveilFrm{{$curr_op->operation_id}}" action="index.php?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="sortie_reveil" value="" />
              <button class="tick notext" type="submit" onclick="this.form.sortie_reveil.value = 'current'"></button>
            </form>
          </td>
        </tr>
        {{/foreach}}
      </table>