<script type="text/javascript">

  Main.add(function () {
    Calendar.regField(getForm("dateMonitor").date, null, {noView: true});
  });
  
  reloadMonitor = function(oForm) {
    alert('refresh');
    return false;
  }

</script>

<form action="?" name="dateMonitor" method="get" onsubmit="return reloadMonitor(this)">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="main">
  <tr>
    <th class="title halfPane">
      <select name="salle_id" onchange="this.form.onsubmit()">
      {{foreach from=$listBlocs item=_bloc}}
        <optgroup label="{{$_bloc}}">
          {{foreach from=$_bloc->_ref_salles item=_salle}}
          <option value={{$_salle->_id}} {{if $_salle->_id == $salle->_id}}selected="selected"{{/if}}>
            {{$_salle->_shortview}}
          </option>
          {{/foreach}}
        </optgroup>
      {{/foreach}}
      </select>
    </th>
    <th class="title">
      à partir du {{$date|date_format:$conf.longdate}}
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.onsubmit()" />
    </th>
  </tr>
</table>

</form>

<table class="planningBloc">
  <tr>
    <th class="narrow">Date</th>
    {{foreach from=$listHours item=_hour}}
    <th colspan="4" class="heure">{{$_hour}}:00</th>
    {{/foreach}}
  </tr>
  {{foreach from=$listDays key=curr_day item=plagesPerDay}}
  {{assign var="keyHorsPlage" value="$curr_day-HorsPlage"}}
  <tr>
    <td class="salle" style="width: 1%; white-space: nowrap;" {{if $affichages.$keyHorsPlage|@count}}rowspan="2"{{/if}}>
      {{$curr_day|date_format:"sem. %U | %d/%m"}}
    </td>
    {{foreach from=$listHours item=_hour}}
    {{foreach from=$listMins item=_min}}
      {{assign var="creneau" value="$curr_day-$_hour:$_min:00"}}
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
          {{if $can->edit}}
          <strong title="{{$plage->_fill_rate}} % du temps occupé">
          <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$plage->plageop_id}}" title="Agencer les interventions">
            {{$plage->_view}}
          </a>
          ({{$plage->_nb_operations_placees}}/{{$plage->_nb_operations}})
          </strong>
          <a onclick="EditPlanning.edit('{{$plage->plageop_id}}','{{$curr_day}}');" href="#">
            <img src="images/icons/edit.png" title="Editer la plage" border="0" height="16" width="16" />
          </a>
          {{assign var=affectations value=$plage->_ref_affectations_personnel}}
  
          {{if ($affectations.op|@count) || ($affectations.op_panseuse|@count) || ($affectations.iade|@count)}}
            <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$plage->plageop_id}}">
            <img src="images/icons/personnel.png" border="0" height="16" width="16" 
                 onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-plage-{{$plage->_id}}")' />
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
  </tr>
  {{if $affichages.$keyHorsPlage|@count}}
  <tr>
    <td colspan="100" class="empty">
      <a href="?m=dPbloc&tab=vw_urgences&date={{$curr_day}}">
        + {{$affichages.$keyHorsPlage|@count}} intervention(s) hors plage
      </a>
    </td>
  </tr>
  {{/if}}
  {{/foreach}}
</table>
