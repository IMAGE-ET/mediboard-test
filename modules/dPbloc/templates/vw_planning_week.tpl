<table class="main">
  <tr>
    <td class="greedyPane">
      <table id="planningBloc">
      {{foreach from=$listDays key=curr_day item=plagesPerDay}}
        <tr>
          <th><strong>{{$curr_day|date_format:"%a %d %b"}}</strong></th>
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
              <td nowrap="nowrap" style="text-align: center;white-space: normal;background-color:#{{$colorCell}};" colspan="{{$arrayAffichage.$keyAff->_nbQuartHeure}}">
                <strong>
                <a href="index.php?m=dPbloc&amp;tab=vw_edit_interventions&amp;id={{$arrayAffichage.$keyAff->id}}" target="_self">
                  {{$arrayAffichage.$keyAff->_view}}
                </a> ({{$arrayAffichage.$keyAff->_nb_operations}})
                <a href="index.php?m=dPbloc&amp;tab=vw_edit_planning&amp;id={{$arrayAffichage.$keyAff->id}}&amp;date={{$curr_day}}">
                  <img src="./modules/dPbloc/images/edit.png" alt="editer la plage" border="0" height="16" width="16" />
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
     {{include file="vw_idx_planning.tpl"}}
   </td>
  </tr>
</table>