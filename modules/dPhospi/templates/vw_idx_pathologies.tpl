<script type="text/javascript">

function reloadPatient(oForm) {
  var url_patient = new Url;
  url_patient.setModuleAction("dPhospi", "httpreq_pathologies");
  url_patient.addParam("sejour_id", oForm.sejour_id.value);
  url_patient.requestUpdate('sejour-'+oForm.sejour_id.value, { waitingText : null });

}

</script>

<table>
<tr>

{{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
<td  style="vertical-align: top">
<table class="tbl">
<tr>
  <th class="title">
    {{tr}}CSejour.groupe.{{$group_name}}{{/tr}}
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




  