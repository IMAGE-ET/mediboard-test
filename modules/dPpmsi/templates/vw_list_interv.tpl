<script type="text/javascript">

Main.add(function () {
  Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
});

</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="9">
      Liste des {{$totalOp}} intervention(s) du {{$date|date_format:$dPconfig.longdate}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
    <th>Traité</th>
    <th>Dossier</th>
    <th>{{mb_label object=$operation field="chir_id"}}</th>
    <th>Patient</th>
    <th>Heure</th>
    <th>{{mb_label object=$operation field="libelle"}}</th>
    <th>Codes prévus</th>
    <th>{{mb_label object=$operation field="labo"}}</th>
    <th>{{mb_label object=$operation field="anapath"}}</th>
  </tr>
  {{foreach from=$plages item=curr_plage}}
  {{foreach from=$curr_plage->_ref_operations item=curr_op}}
  <tr>
    <td>
      {{if $curr_op->_ref_hprim_files|@count}}
       <img src="images/icons/tick.png" alt="ok" />
      {{else}}
      <img src="images/icons/cross.png" alt="alerte" />
      {{/if}}
    </td>
    <td>
      [{{$curr_op->_ref_sejour->_num_dossier}}]
    </td>
    <td class="text">
      Dr {{$curr_op->_ref_chir->_view}}
    </td>
    <td class="text">
      <a title="Voir le dossier PMSI" href="?m=dPpmsi&amp;tab=vw_dossier&amp;pat_id={{$curr_op->_ref_sejour->patient_id}}">
        {{$curr_op->_ref_sejour->_ref_patient->_view}}
        [{{$curr_op->_ref_sejour->_ref_patient->_IPP}}]
      </a>
    </td>
    <td>
      {{if $curr_op->rank}}
        {{$curr_op->time_operation|date_format:$dPconfig.time}}
      {{else}}
        NP
      {{/if}}
    </td>
    <td class="text">
      {{$curr_op->libelle}}
    </td>
    <td class="text">
      <ul>
      {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        <li>{{$curr_code->code}}</li>
      {{/foreach}}
      </ul>
    </td>
    <td class="text">
      {{mb_value object=$curr_op field="labo"}}
    </td>
    <td class="text">
      {{mb_value object=$curr_op field="anapath"}}
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{if $urgences|@count}}
  <tr>
    <th colspan="6">Urgences</th>
  </tr>
  {{foreach from=$urgences item=curr_op}}
  <tr>
    <td>
      {{if $curr_op->_ref_hprim_files|@count}}
       <img src="images/icons/tick.png" alt="ok" />
      {{else}}
      <img src="images/icons/cross.png" alt="alerte" />
      {{/if}}
    </td>
    <td class="text">
      Dr {{$curr_op->_ref_chir->_view}}
    </td>
    <td class="text">
      <a title="Voir le dossier PMSI" href="?m=dPpmsi&amp;tab=vw_dossier&amp;pat_id={{$curr_op->_ref_sejour->patient_id}}">
        {{$curr_op->_ref_sejour->_ref_patient->_view}}
      </a>
    </td>
    <td>Urgence</td>
    <td class="text">
      {{$curr_op->libelle}}
    </td>
    <td class="text">
      <ul>
      {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        <li>{{$curr_code->code}}</li>
      {{/foreach}}
      </ul>
    </td>
    <td class="text">
      {{mb_value object=$curr_op field="labo"}}
    </td>
    <td class="text">
      {{mb_value object=$curr_op field="anapath"}}
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>