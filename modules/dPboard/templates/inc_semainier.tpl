<table class="main">
  <tr>
    <td>
      <table id="weeklyPlanning">
        <tr>
          <th></th>
          {{foreach from=$plagesConsult|smarty:nodefaults key=curr_day item=plagesPerDay}}
          <th colspan="2">{{$curr_day|date_format:"%A %d"}}</th>
          {{/foreach}}
        </tr>  
        {{foreach from=$listHours|smarty:nodefaults item=curr_hour}}
        <tr>
          <th rowspan="4">{{$curr_hour}}h</th>
          {{foreach from=$listMins|smarty:nodefaults item=curr_mins key=keyMins}}
          {{if $keyMins}}
          </tr><tr>
          {{/if}}
          {{foreach from=$plagesConsult key=curr_day item=plagesPerDay}}
          
          {{assign var="keyAff" value="$curr_day $curr_hour:$curr_mins"}}
          {{assign var="plageJour" value=$aAffichage.$keyAff}}
          
          {{if is_string($plageJour.plagesOp) && $plageJour.plagesOp == "empty" &&
               is_string($plageJour.plagesConsult) && $plageJour.plagesConsult == "empty"}}
               <td class="empty" colspan="2"></td>
          {{elseif is_string($plageJour.plagesOp) && $plageJour.plagesOp == "hours" &&
                   is_string($plageJour.plagesConsult) && $plageJour.plagesConsult == "hours"}}
                <td class="empty" rowspan="4" colspan="2"></td>
          {{elseif is_string($plageJour.plagesOp) && $plageJour.plagesOp == "full" &&
                   is_string($plageJour.plagesConsult) && $plageJour.plagesConsult == "full"}}
          {{else}}
            {{assign var="colonne" value="plagesConsult"}}
            {{include file="inc_cellule_semainier.tpl"}}
            
            {{assign var="colonne" value="plagesOp"}}
            {{include file="inc_cellule_semainier.tpl"}}
          {{/if}}
          {{/foreach}}
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>