<script type="text/javascript">

// faire le submit de formOperation dans le onComplete de l'ajax
var checkPersonnel = function(oFormAffectation, oFormOperation){
  oFormOperation.entree_reveil.value = 'current';
  // si affectation renseignée, on submit les deux formulaires
  if(oFormAffectation && oFormAffectation.personnel_id.value != ""){
    submitFormAjax(oFormAffectation, 'systemMsg', {onComplete: oFormOperation.submit.bind(oFormOperation)} );
  }
  else {
  // sinon, on ne submit que l'operation
    oFormOperation.submit();
  }
}

regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab=vw_reveil&date=");
</script>

      <form action="?" name="selection" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <table class="form">
        <tr>
          <th class="category">{{$listOps|@count}} patient(s) en attente</th>
          <th class="category" colspan="2">
            <div style="float: right;">{{$hour|date_format:"%Hh%M"}}</div>
            {{$date|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
          </th>
        </tr>
      </table>
      </form>

      <table class="tbl">
        <tr>
          <th>Salle</th>
          <th>Praticien</th>
          <th>Patient</th>
          <th>Sortie Salle</th>
          <th>Entrée reveil</th>
        </tr>    
        {{foreach from=$listOps item=curr_op}}
        <tr>
          <td>{{$curr_op->_ref_salle->nom}}</td>
          <td class="text">Dr. {{$curr_op->_ref_chir->_view}}</td>
          <td class="text">{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
          <td>
            {{if $can->edit}}
              <form name="editSortieBlocFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
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
          <td>
            {{if $can->edit || $modif_operation}}
            
            {{if $personnels !== null}}
            <form name="selPersonnel{{$curr_op->_id}}" action="?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPpersonnel" />
              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="object_id" value="{{$curr_op->_id}}" />
              <input type="hidden" name="object_class" value="{{$curr_op->_class_name}}" />
              <input type="hidden" name="tag" value="reveil" />
              <input type="hidden" name="realise" value="0" />
              <select name="personnel_id">
              <option value="">&mdash; Personnel</option>
              {{foreach from=$personnels item="personnel"}}
              <option value="{{$personnel->_id}}">{{$personnel->_ref_user->_view}}</option>
              {{/foreach}}
              </select>
            </form>
            {{/if}}
            
            <form name="editEntreeReveilFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="entree_reveil" value="" /> 
              <button class="tick notext" type="button" onclick="checkPersonnel(document.selPersonnel{{$curr_op->_id}}, this.form);">{{tr}}Modify{{/tr}}</button>
            </form>
            {{else}}
              -
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>
      
      </form>