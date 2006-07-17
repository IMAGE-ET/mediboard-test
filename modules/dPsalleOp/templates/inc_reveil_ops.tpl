<script type="text/javascript">
regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab=vw_reveil&date=");
</script>

      <form action="index.php" name="selection" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <table class="form">
        <tr>
          <th class="category">{{$listOps|@count}} patients en attente</th>
          <th class="category" colspan="2">
            <div style="float: right;">{{$hour|date_format:"%Hh%M"}}</div>
            {{$date|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
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
            <form name="editEntreeReveilFrm{{$curr_op->operation_id}}" action="index.php?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="entree_reveil" value="" />
              <button class="tick notext" type="submit" onclick="this.form.entree_reveil.value = 'current'"></button>
            </form>
          </td>
        </tr>
        {{/foreach}}
      </table>
      
      </form>