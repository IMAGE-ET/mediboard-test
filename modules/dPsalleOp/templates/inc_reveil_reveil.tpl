<script type="text/javascript">

var submitReveil = function(oFormAffectation, oFormOperation){
  oFormOperation.entree_reveil.value = '';
  // s'il y a une affectation, on submit les deux formulaires
  if(oFormAffectation.affect_id.value != ""){
    submitFormAjax(oFormAffectation, 'systemMsg', {onComplete: oFormOperation.submit.bind(oFormOperation)} ); 
  }
  else {
  // sinon, on ne submit que l'operation
    oFormOperation.submit();  
  }
}

</script>
        
      <table class="form">
        <tr>
          <th class="category">
            {{if $hour}}<div style="float: right;">{{$hour|date_format:"%Hh%M"}}</div>{{/if}}
            {{$listReveil|@count}} patients en salle de reveil
          </th>
        </tr>
      </table>

      <table class="tbl">
        <tr>
          <th>Salle</th>
          <th>Praticien</th>
          <th>Patient</th>
          <th>Chambre</th>
          <th>Sortie Salle</th>
          <th>Entr�e reveil</th>
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
            Non plac�
            {{/if}}
          </td>
          <td class="button">
            {{if $can->edit}}
              <form name="editSortieBlocFrm{{$curr_op->operation_id}}" action="index.php?m={{$m}}" method="post">
                <input type="hidden" name="m" value="dPplanningOp" />
                <input type="hidden" name="dosql" value="do_planning_aed" />
                <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
                <input type="hidden" name="del" value="0" />
	            <input name="sortie_salle" size="5" type="text" value="{{$curr_op->sortie_salle|date_format:"%H:%M"}}">
	            <button class="tick notext" type="submit">{{tr}}Modify{{/tr}}</button>
	          </form>
            {{else}}
            {{$curr_op->sortie_salle|date_format:"%Hh%M"}}
            {{/if}}
          </td>
          <td class="button">
          
            
            <form name="delPersonnel{{$curr_op->_id}}" action="index.php?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPpersonnel" />
              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="affect_id" value="{{$curr_op->_ref_affectation_personnel->_id}}" />
            </form>
            
            
            <form name="editSortieBlocFrm{{$curr_op->operation_id}}" action="index.php?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
              <input type="hidden" name="del" value="0" />
              {{if $curr_op->_ref_sejour->type=="exte"}}
              -
              {{elseif $can->edit}}
	          <input name="entree_reveil" size="5" type="text" value="{{$curr_op->entree_reveil|date_format:"%H:%M"}}">
	          <button class="tick notext" type="submit">{{tr}}Modify{{/tr}}</button>
	          {{if $curr_op->_ref_affectation_personnel->_id}}
	          {{$curr_op->_ref_affectation_personnel->_ref_user->_view}}
	          {{/if}}
	          <button class="cancel notext" type="button" onclick="submitReveil(document.delPersonnel{{$curr_op->_id}}, this.form);">{{tr}}Cancel{{/tr}}</button>
              {{elseif $modif_operation}}
              <select name="entree_reveil" onchange="this.form.submit()">
                <option value="">-</option>
                {{foreach from=$timing.$key.entree_reveil|smarty:nodefaults item=curr_time}}
                <option value="{{$curr_time}}" {{if $curr_time == $curr_op->entree_reveil}}selected="selected"{{/if}}>
                  {{$curr_time|date_format:"%Hh%M"}}
                </option>
                {{/foreach}}
              </select>
              <button class="cancel notext" type="submit" onclick="this.form.entree_reveil.value = ''">{{tr}}Cancel{{/tr}}</button>
              {{else}}
                {{$curr_op->entree_reveil|date_format:"%Hh%M"}}
              {{/if}}
            </form>
          </td>
          <td class="button">
            {{if $can->edit || $modif_operation}}
            <form name="editEntreeReveilFrm{{$curr_op->operation_id}}" action="index.php?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="sortie_reveil" value="" />
              <button class="tick notext" type="submit" onclick="this.form.sortie_reveil.value = 'current'">{{tr}}Modify{{/tr}}</button>
            </form>
            {{else}}-{{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>