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
        {{foreach from=$listPrats item=curr_prat}}
        <option value="{{$curr_prat->_id}}" {{if $curr_prat->_id == $praticien_id}} selected="selected" {{/if}}>
          {{$curr_prat->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
</table>

</form>

<script type="text/javascript">
	regRedirectPopupCal("{{$date}}", "?m={{$m}}&op=0&date=");
</script>
      
{{include file="inc_details_op_prat.tpl"}}