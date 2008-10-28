
<form action="?" name="selection" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="op" value="0" />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  
  <tr>
    <th><label for="salle" title="Salle d'opération">Salle</label></th>
    <td>
      <select name="salle" onchange="this.form.submit()">
        <option value="">&mdash; {{tr}}CSalle.none{{/tr}}</option>
        {{foreach from=$listBlocs item=curr_bloc}}
        <optgroup label="{{$curr_bloc->nom}}">
          {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
          <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $salle->_id}}selected="selected"{{/if}}>
            {{$curr_salle->nom}}
          </option>
          {{foreachelse}}
          <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
          {{/foreach}}
        </optgroup>
        {{/foreach}}
      </select>
    </td>
  </tr>
</table>

</form>

<script type="text/javascript">
	regRedirectPopupCal("{{$date}}", "?m={{$m}}&op=0&date=");
</script>
      
{{include file="inc_details_plages.tpl"}}