{{mb_script module=ssr script=stats}}

<script type="text/javascript">
  Main.add(function() {
    Calendar.regField(getForm("MacroStats").date);
  });
</script>

<form name="MacroStats" method="get">
  <input type="hidden" name="date" value="{{$date}}" />
  <select name="period">
    <option value="day"  >{{tr}}Day  {{/tr}}</option>
    <option value="week" >{{tr}}Week {{/tr}}</option>
    <option value="month">{{tr}}Month{{/tr}}</option>
    <option value="year" >{{tr}}Year {{/tr}}</option>
  </select>
  <select name="type">
    <option value="CEvenementSSR">{{tr}}CEvenementSSR{{/tr}}</option>
    <option value="CActeCdARR"   >{{tr}}CActeCdARR{{/tr}}   </option>
    <option value="CActeCsARR"   >{{tr}}CActeCsARR{{/tr}}   </option>
  </select>
  <button class="modify" type="button" onclick="Stats.reeducateurs(this);">
    {{tr}}mod-ssr-tab-reeducateur_stats{{/tr}}
  </button> 
</form>
