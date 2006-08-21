<table class="main">
  <tr>
    <td class="greedyPane">
      <table id="planningBloc">
      {{foreach from=$listDays key=curr_day item=plagesPerDay}}
        <tr>
          <th>
            <a href="index.php?m=dPbloc&amp;tab=vw_edit_planning&amp;date={{$curr_day}}">
              <strong>{{$curr_day|date_format:"%a %d %b"}}</strong>
            </a>
          </th>
          {{foreach from=$listHours item=curr_hours}}
          <th colspan="4" class="heure">{{$curr_hours}}:00</th>
          {{/foreach}}         
        </tr>
        {{foreach from=$listSalles item=curr_salle key=keySalle}}
        <tr>
          <td class="salle">{{$curr_salle->nom}}</td>
          {{foreach from=$listHours item=curr_hour}}
          {{foreach from=$listMins item=curr_min key=keymin}}
            {{assign var="keyAff" value="$curr_day-s$keySalle-$curr_hour:$curr_min"}}
            
            {{if is_string($arrayAffichage.$keyAff) &&  $arrayAffichage.$keyAff== "empty"}}
              <td class="empty{{if !$keymin}} firsthour{{/if}}"></td>
            {{elseif is_string($arrayAffichage.$keyAff) &&  $arrayAffichage.$keyAff== "full"}}
           
            {{else}}
              {{if $arrayAffichage.$keyAff->chir_id}}
                {{assign var=colorCell value=$arrayAffichage.$keyAff->_ref_chir->_ref_function->color}}
              {{else}}
                {{assign var=colorCell value=$arrayAffichage.$keyAff->_ref_spec->color}}
              {{/if}}
              
              {{assign var="pct" value=$arrayAffichage.$keyAff->_fill_rate}}
              {{if $pct gt 100}}
              {{assign var="pct" value=100}}
              {{/if}}
              {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
              {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
              {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
              {{else}}{{assign var="backgroundClass" value="full"}}
              {{/if}}
              <td nowrap="nowrap" style="vertical-align: top; text-align: center;white-space: normal;background-color:#{{$colorCell}};" colspan="{{$arrayAffichage.$keyAff->_nbQuartHeure}}" title="{{$arrayAffichage.$keyAff->_fill_rate}} % du temps occupé">
                <div class="progressBar" style="height: 3px;">
                  <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;height: 3px;border-right: 2px solid #000;">
                  </div>
                </div>
                <strong>
                <a href="index.php?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$arrayAffichage.$keyAff->plageop_id}}" title="Agencer les interventions">
                  {{$arrayAffichage.$keyAff->_view}}
                </a> ({{$arrayAffichage.$keyAff->_nb_operations}})
                <a href="index.php?m=dPbloc&amp;tab=vw_edit_planning&amp;plageop_id={{$arrayAffichage.$keyAff->plageop_id}}&amp;date={{$curr_day}}">
                  <img src="./modules/dPbloc/images/edit.png" alt="Editer la plage" title="Editer la plage" border="0" height="16" width="16" />
                </a>
                </strong>
              </td>
            {{/if}}
           {{/foreach}}
          {{/foreach}}
        </tr>
        {{/foreach}} 
      {{/foreach}}
      </table>
   
   
   </td>
   <td>
     {{include file="inc_legende_planning.tpl"}}
   </td>
  </tr>
</table>