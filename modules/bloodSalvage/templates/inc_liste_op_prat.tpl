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
    <th><label for="praticien_id" title="Praticien">Praticien</label></th>
    <td>
      <select name="praticien_id" onchange="this.form.submit()">
        <option value="">&mdash; Aucun praticien</option>
        {{foreach from=$listPrats key=prat_id item=prat_view}}
        <option value="{{$prat_id}}" {{if $prat_id == $praticien->_id}} selected="selected" {{/if}}>
          {{$prat_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
</table>

</form>

<script type="text/javascript">
	Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&op=0&date=");
</script>
      
{{include file="inc_details_op_prat.tpl"}}