<script type="text/javascript">
  Main.add(function() {
    var form = getForm("filterDate");
    Calendar.regField(form.date);
  });
</script>

<form name="filterDate" method="get" action="?">
  <input type="hidden" name="m" value="maternite" />
  <input type="hidden" name="tab" value="vw_grossesses" />
  <strong>
    <a href="#1" onclick="var form = getForm('filterDate'); $V(form.date, this.get('date')); form.submit();" data-date="{{$date_min}}">&lt;&lt;&lt;</a>
    <input type="hidden" name="date" value="{{$date}}" class="notNull" onchange="this.form.submit()"/>
    <a href="#1" onclick="var form = getForm('filterDate'); $V(form.date, this.get('date')); form.submit();" data-date="{{$date_max}}"">&gt;&gt;&gt;</a>
  </strong>
</form>

<table class="tbl">
  <tr>
    <th class="category" colspan="2">Grossesses arrivant à terme entre le {{$date_min|date_format:$conf.date}} et le {{$date_max|date_format:$conf.date}}</th>
  </tr>
  {{foreach from=$grossesses item=_grossesse}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_grossesse->_guid}}')">
          {{$_grossesse->_ref_parturiente}}
        </span>
      </td>
      <td>
        {{$_grossesse->terme_prevu|date_format:$conf.date}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="2">{{tr}}CGrossesse.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
