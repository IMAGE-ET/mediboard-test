{{mb_script module=ssr script=stats}}

<script type="text/javascript">
  Main.add(function() {
    Calendar.regField(getForm("MacroStats").date);
  });
</script>

<form name="MacroStats" method="get">

<table class="form">
  <tr>
    <th>
      <label for="date">Date de référence</label>
    </th>
    <td>
      <input type="hidden" name="date" value="{{$date}}" />
    </td>
  </tr>

  <tr>
    <th>
      <label for="period">Période</label>
    </th>
    <td>
      <select name="period">
        <option value="day"  >{{tr}}Day  {{/tr}}</option>
        <option value="week" >{{tr}}Week {{/tr}}</option>
        <option value="month">{{tr}}Month{{/tr}}</option>
        <option value="year" >{{tr}}Year {{/tr}}</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="type">Type de statistiques</label>
    </th>
    <td>
      <select name="type">
        <option value="CEvenementSSR">{{tr}}CEvenementSSR{{/tr}}</option>
        <option value="CActeSSR"     >{{tr}}CActeSSR{{/tr}}     </option>
      </select>
    </td>
  </tr>

  <tr>
    <td colspan="2" class="button">
      <button class="modify" type="button" onclick="Stats.reeducateurs(this);">
        {{tr}}mod-ssr-tab-reeducateur_stats{{/tr}}
      </button>
    </td>
  </tr>


</table>

</form>
