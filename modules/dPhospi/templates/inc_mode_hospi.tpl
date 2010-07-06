<th>
  <form name="chgMode" action="?m={{$m}}" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <label for="mode" title="Veuillez choisir un type de vue">Type de vue</label>
</th>
<td>
  <select name="mode" onchange="submit()" style="width:135px">
    <option value="0" {{if $mode == 0}}selected="selected"{{/if}}>Vue instantanée</option>
    <option value="1" {{if $mode == 1}}selected="selected"{{/if}}>Vue de la journée</option>
  </select>
  </form>
</td>
