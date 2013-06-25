{{mb_default var=salle_id value=""}}
{{assign var=sSalle value=$salle_id|ternary:"-s$salle_id":""}}
{{foreach from=$listHours item=_hour}}
{{foreach from=$listMins item=_min}}
  {{assign var="creneau" value="$curr_day$sSalle-$_hour:$_min:00"}}
  {{assign var=affichage value=$affichages.$creneau}}
  
  {{if $affichage === "empty"}}
    <td class="empty{{if $_min == "00"}} firsthour{{/if}}"></td>
    {{elseif $affichage === "full"}}
   
    {{else}}
    {{assign var=_listPlages value=$listPlages.$curr_day}}
    {{assign var=plage value=$_listPlages.$affichage}}
 
      {{mb_ternary var=color test=$plage->chir_id value=$plage->_ref_chir->_ref_function->color other=$plage->_ref_spec->color}}
     
      {{assign var="pct" value=$plage->_fill_rate}}
      {{if $pct gt 100}}
      {{assign var="pct" value=100}}
      {{/if}}
      {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
      {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
      {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
      {{else}}{{assign var="backgroundClass" value="full"}}
      {{/if}}
      <td class="plageop" style="background:#{{$color}};" colspan="{{$plage->_nbQuartHeure}}">
      {{mb_include module=system template=inc_object_notes object=$plage}}
      <div class="progressBar" style="height: 3px;" title="{{$plage->_fill_rate}} % du temps occupé">
        <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;height: 3px;border-right: 2px solid #000;">
        </div>
      </div>
      {{if $bloc->_canEdit}}
      <strong title="{{$plage->_fill_rate}} % du temps occupé">
      <a onclick="EditPlanning.order('{{$plage->_id}}');" href="#" title="Agencer les interventions">
        {{$plage->_view}}
      </a>
      ({{$plage->_nb_operations_placees}}/{{$plage->_nb_operations}})
      </strong>
      <a onclick="EditPlanning.edit('{{$plage->_id}}','{{$curr_day}}');" href="#" class="not-printable">
        <img src="images/icons/edit.png" title="Modifier la plage" border="0" height="16" width="16" />
      </a>
      {{if $plage->verrouillage == "oui"}}
        <img src="style/mediboard/images/buttons/lock.png" title="Plage verrouillée" border="0" height="16" width="16" />
      {{/if}}
      {{assign var=affectations value=$plage->_ref_affectations_personnel}}

      {{if ($affectations.op|@count) || ($affectations.op_panseuse|@count) || ($affectations.iade|@count)}}
        <a onclick="EditPlanning.order('{{$plage->_id}}');" href="#" title="Agencer les interventions">
        <img src="images/icons/personnel.png" border="0" height="16" width="16" 
             onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-plage-{{$plage->_id}}")' />
         {{mb_include module=system template=inc_vw_counter_tip count=$plage->_count_affectations_personnel}}
        </a>
        <div id="tooltip-content-plage-{{$plage->_id}}" style="display: none; width: 200px;">
          <table class="tbl">
            {{foreach from=$affectations key=type_personnel item=_affectations}}
              {{if $type_personnel == "op" || $type_personnel == "op_panseuse" || $type_personnel == "iade"}} 
              <tr>
                <th>{{tr}}CPersonnel.emplacement.{{$type_personnel}}{{/tr}}</th>
              </tr>
              {{foreach from=$_affectations item=_affectation}}
              <tr>
                <td class="text">
                  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_affectation->_ref_personnel->_ref_user}}
                </td>
              </tr>
              {{foreachelse}}
              <tr>
                <td class="empty">{{tr}}None{{/tr}}</td>
              </tr>
              {{/foreach}} 
              {{/if}}
            {{/foreach}}
          </table>
        </div>
      
      {{/if}}
      {{else}}
      <strong title="{{$plage->_fill_rate}} % du temps occupé">
        {{$plage->_view}}
        ({{$plage->_nb_operations_placees}}/{{$plage->_nb_operations}})
      </strong>
      {{/if}}
    </td>
  {{/if}}
{{/foreach}}
{{/foreach}}