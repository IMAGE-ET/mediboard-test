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
              {{include file="inc_cellule_semainier.tpl" nodebug=true}}
            {{/if}}
            
            {{foreach from=$listEntry.$curr_day.salle item=curr_salle}}
              {{assign var="colonne" value="Salle$curr_salle"}}
              {{assign var="style" value="Op"}}
              {{include file="inc_cellule_semainier.tpl" nodebug=true}}
            {{/foreach}}

          {{/foreach}}
          {{/foreach}}
        {{/foreach}}
      </table>
      <div style="width:100px;">
        Légende
        <table id="weeklyPlanning" class="tbl">
          <tr>
            <td class="nonEmptyConsult">&nbsp;</td>
            <td>Plage de consultation</td>
          </tr>
          <tr>
            <td class="nonEmptyOp">&nbsp;</td>
            <td>Plage opératoire</td>
          </tr>
        </table>
      </div>
    </td>
    <td id="viewTooltip" style="width: 300px; font-size: 70%;">
      {{foreach from=$listHours|smarty:nodefaults item=curr_hour}}
        {{foreach from=$listMins|smarty:nodefaults item=curr_mins key=keyMins}}
        {{foreach from=$plagesConsult key=curr_day item=plagesPerDay}}
          
          {{assign var="keyAff" value="$curr_day $curr_hour:$curr_mins"}}
          {{assign var="plageJour" value=$aAffichage.$keyAff}}
            
            {{if $listEntry.$curr_day.consult || !$listEntry.$curr_day.nbcol}}
              {{assign var="colonne" value="plagesConsult"}}
              {{assign var="style" value="Consult"}}
              {{assign var="plageInfos" value=$plageJour.$colonne}}
              {{if !is_string($plageJour.$colonne)}}
                <div id="{{$plageInfos->_class_name}}{{$plageInfos->_id}}" class="" style="display: none;"></div>
              {{/if}} 
            {{/if}}
            
            {{foreach from=$listEntry.$curr_day.salle item=curr_salle}}
              {{assign var="colonne" value="Salle$curr_salle"}}
              {{assign var="style" value="Op"}}
              {{assign var="plageInfos" value=$plageJour.$colonne}}
              {{if !is_string($plageJour.$colonne)}}
                <div id="{{$plageInfos->_class_name}}{{$plageInfos->_id}}" class="" style="display: none;"></div>
              {{/if}} 
            {{/foreach}}

        {{/foreach}}
        {{/foreach}}
      {{/foreach}}
    </td>
  </tr>
</table>