<table class="main">
  <tr>
    <td>
      <table id="weeklyPlanning" class="tbl">
        <tr>
          <th></th>
          {{foreach from=$plagesConsult|smarty:nodefaults key=curr_day item=plagesPerDay}}
          <th colspan="{{if !$listEntry.$curr_day.nbcol}}1{{else}}{{$listEntry.$curr_day.nbcol}}{{/if}}">
            <a href="?m=dPboard&amp;tab=vw_mainboard&amp;view=day&amp;date={{$curr_day}}">
              {{$curr_day|date_format:"%A %d"}}
            </a>
          </th>
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
            
            {{if $listEntry.$curr_day.consult || !$listEntry.$curr_day.nbcol}}
              {{assign var="colonne" value="plagesConsult"}}
              {{assign var="style" value="Consult"}}
              {{include file="inc_cellule_semainier.tpl"}}
            {{/if}}
            
            {{foreach from=$listEntry.$curr_day.salle item=curr_salle}}
              {{assign var="colonne" value="Salle$curr_salle"}}
              {{assign var="style" value="Op"}}
              {{include file="inc_cellule_semainier.tpl"}}
            {{/foreach}}

          {{/foreach}}
          {{/foreach}}
        {{/foreach}}
      </table>
      <div style="width:100px;">
        L�gende
        <table id="weeklyPlanning" class="tbl">
          <tr>
            <td class="nonEmptyConsult">&nbsp;</td>
            <td>Plages de consultations</td>
          </tr>
          <tr>
            <td class="nonEmptyOp">&nbsp;</td>
            <td>Plages op�ratoire</td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>