<table class="main">
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