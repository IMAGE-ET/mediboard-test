<div id="planning-{{$sejour->_guid}}">
  {{mb_script module=ssr script=planning ajax=true}}
  <script>
    Main.add(function () {
      Calendar.regField(getForm("changeDatePlaning").debut, null, {noView: true});
    });
  </script>
  <table class="main">
    <tr>
      <th>
        <div id="patient_banner">
          {{mb_include module=soins template=inc_patient_banner patient=$sejour->_ref_patient}}
        </div>
      </th>
    </tr>
    <tr>
      <th>
        <form action="?" name="changeDatePlaning" method="get" onsubmit="return PlanningSejour.changeDate(this);">
          <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
          <button type="button" class="left notext" onclick="$V($(this).getSurroundingForm().debut, '{{$precedent}}')"></button>
          Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
          <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="return PlanningSejour.changeDate(this.form);" />
          <button type="button" class="right notext" onclick="$V($(this).getSurroundingForm().debut, '{{$suivant}}')"></button>
        </form>
      </th>
    </tr>
    <tr>
      <td>
        <div id="planning-sejour">
          {{mb_include module=system template=calendars/vw_week}}
        </div>
      </td>
    </tr>
  </table>
</div>