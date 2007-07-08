<script type="text/javascript">

function reloadPatient(oForm) {
  var url_patient = new Url;
  url_patient.setModuleAction("dPhospi", "httpreq_pathologies");
  url_patient.addParam("sejour_id", oForm.sejour_id.value);
  url_patient.requestUpdate('sejour-'+oForm.sejour_id.value, { waitingText : null });
}

function pageMain() {
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table>
  <tr>
    <th colspan="4">
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
    {{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
    <td  style="vertical-align: top">
      <table class="tbl">
        <tr>
          <th class="title">
            {{tr}}CSejour.groupe.{{$group_name}}{{/tr}} ({{$sejourNonAffectes|@count}})
          </th>
        </tr>
      </table>
      <table class="tbl">
        {{foreach from=$sejourNonAffectes item=curr_sejour}}
        <tbody id="sejour-{{$curr_sejour->sejour_id}}">
          {{include file="inc_pathologies.tpl"}}
        </tbody>
        {{/foreach}}
      </table>
    </td>
    {{/foreach}}
  </tr>
</table>  