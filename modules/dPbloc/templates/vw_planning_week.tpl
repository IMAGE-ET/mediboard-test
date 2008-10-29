<table class="main">
  <tr>
    <td>
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_planning_week" />
        <select name="bloc_id" onchange="this.form.submit();">
          {{foreach from=$listBlocs item=curr_bloc}}
            <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
              {{$curr_bloc->nom}}
            </option>
          {{foreachelse}}
            <option value="" disabled="disabled">{{tr}}CBlocOperatoire.none{{/tr}}</option>
          {{/foreach}}
        </select>
      </form>
    </td>
  </tr>
  <tr>
    <td class="greedyPane">
      <table id="planningBloc">
      {{foreach from=$listDays key=curr_day item=plagesPerDay}}
        {{include file="inc_planning_day.tpl"}}
      {{/foreach}}
      </table>
   </td>
   <td>
     {{include file="inc_legende_planning.tpl"}}
   </td>
  </tr>
</table>