{{mb_script module=ssr script=planning}}
{{assign var=vue_right value=$app->user_prefs.viewWeeklyConsultCalendar}}

<script type="text/javascript">

setClose = function(time, plage_id, plage_date, chir_id) {
  window.parent.PlageConsultSelector.set(time, plage_id, plage_date, chir_id);
  window.close();
  var form = window.parent.getForm(window.parent.PlageConsultSelector.sForm);
  if (Preferences.choosePatientAfterDate == 1 && !$V(form.patient_id) && !form._pause.checked) {
    window.parent.PatSelector.init();
  }
};

Main.add(function () {
  Calendar.regField(getForm("FilterTop").date, null, {noView: true});
  {{if $vue_right}}
  Calendar.regField(getForm("FilterRight").date, null, {noView: true, inline: true, container: null});
  {{/if}}
  var planning = window["planning-{{$planning->guid}}"];
  ViewPort.SetAvlHeight("plageSelectorTable", 1);
  $('planningWeek').style.height = "1500px";
});

</script>

<div id="plageSelectorTable">

<table class="main layout" style="height: 100%">
  <tr>
    <td>
      
      <form name="FilterTop" action="?" method="get">
      
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="a" value="plage_selector" />
      <input type="hidden" name="dialog" value="1" />
      <input type="hidden" name="function_id" value="{{$function_id}}" />
      <input type="hidden" name="plageconsult_id" value="{{$plage->_id}}" />
  
      <table class="form">
        <tr>
          <th><label for="period" title="Changer la p�riode de recherche">Planning</label></th>
          <td>
            <select name="period" onchange="this.form.submit()">
              {{foreach from=$periods item="_period"}}
              <option value="{{$_period}}" {{if $_period == $period}}selected="selected"{{/if}}>
                {{tr}}Period.{{$_period}}{{/tr}}
              </option>
              {{/foreach}}
            </select>
          </td>
          
          <td>
            <select name="chir_id" style="width: 15em;" onchange="this.form.submit()">
              {{foreach from=$listPraticiens item=curr_praticien}}
              <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $chir_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
                {{$curr_praticien->_view}}
                {{if $app->user_prefs.viewFunctionPrats}}
                  - {{$curr_praticien->_ref_function->_view}}
                {{/if}}
              </option>
             {{/foreach}}
            </select>
          </td>
          
          <td class="button" style="width: 250px;">
            <a style="float:left" href="#1" onclick="$V(getForm('FilterTop').date, '{{$pdate}}')">&lt;&lt;&lt;</a>
            <a style="float:right" href="#1" onclick="$V(getForm('FilterTop').date, '{{$ndate}}')">&gt;&gt;&gt;</a>
            <strong>
              {{$refDate|date_format:" semaine du %d %B %Y (%U)"}}
            </strong>
            <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit();" />
          </td>
        </tr>
  
      </table>
  
      </form>
    </td>
    {{if $vue_right}}
    <td rowspan="2">
      <form name="FilterRight" action="?" method="get">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="a" value="plage_selector" />
      <input type="hidden" name="dialog" value="1" />
      <input type="hidden" name="chir_id" value="{{$chir_id}}" />
      <input type="hidden" name="function_id" value="{{$function_id}}" />
      <input type="hidden" name="plageconsult_id" value="{{$plage->_id}}" />
      <input type="hidden" name="period" value="{{$period}}" />
      <table class="form">
         <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit();" />
      </table>
      </form>
    </td>
    {{/if}}
  </tr>
  <tr>
    <td>
      {{mb_include module=ssr template=inc_vw_week}}
    </td>
  </tr>
</table>
</div>