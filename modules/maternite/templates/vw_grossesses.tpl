<script type="text/javascript">
  Main.add(function() {
    var form = getForm("filterDate");
    Calendar.regField(form.date, null, {noView: true});
  });
</script>

{{mb_script module=admissions script=admissions}}

<form name="filterDate" method="get" action="?">
  <input type="hidden" name="m" value="maternite" />
  <input type="hidden" name="tab" value="vw_grossesses" />
  <strong>
    <a href="#1" onclick="var form = getForm('filterDate'); $V(form.date, this.get('date')); form.submit();" data-date="{{$date_min}}">&lt;&lt;&lt;</a>
    {{$date|date_format:$conf.longdate}}
    <input type="hidden" name="date" value="{{$date}}" class="notNull" onchange="this.form.submit()"/>
    <a href="#1" onclick="var form = getForm('filterDate'); $V(form.date, this.get('date')); form.submit();" data-date="{{$date_max}}"">&gt;&gt;&gt;</a>
  </strong>
</form>

<table class="tbl" id="admissions">
  <tr>
    <th class="title" colspan="5">Grossesses arrivant à terme entre le {{$date_min|date_format:$conf.date}} et le {{$date_max|date_format:$conf.date}}</th>
  </tr>
  <tr>
    <th class="category">
      Terme prévu
    </th>
    <th class="category">
      {{tr}}CPatient{{/tr}}
    </th>
    <th class="category narrow">
      <input type="text" size="3" onkeyup="Admissions.filter(this, 'admissions')" id="filter-patient-name" />
    </th>
    <th class="category">
      {{tr}}CSejour.all{{/tr}}
    </th>
    <th class="category">
    </th>
  </tr>
  {{foreach from=$grossesses item=_grossesse}}
    <tr>
      <td style="width: 8%">
        {{$_grossesse->terme_prevu|date_format:$conf.date}}
      </td>
      <td colspan="2" style="width: 15%">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_grossesse->_guid}}')" class="CPatient-view">
          {{$_grossesse->_ref_parturiente}}
        </span>
      </td>
      <td>
        <ul style="line-height: 1.4em;">
          {{foreach from=$_grossesse->_ref_sejours item=_sejour}}
            <li>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
                {{$_sejour}}
              </span>
            </li>
          {{/foreach}}
        </ul>
      </td>
      <td class="narrow">
        <a class="button new" href="?m=dPplanningOp&tab=vw_edit_sejour&grossesse_id={{$_grossesse->_id}}&sejour_id=0&patient_id={{$_grossesse->parturiente_id}}">Nouveau séjour</a>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">{{tr}}CGrossesse.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
