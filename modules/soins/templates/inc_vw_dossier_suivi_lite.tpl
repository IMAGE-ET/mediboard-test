<script>
  closeModalSuivi = function() {
    Control.Modal.close();
  }
</script>

<table class="tbl">
  <tr>
    <th>Contenu</th>
  </tr>
  {{foreach from=$suivi item=_suivi}}
    <tr>
      <td style="white-space : normal;">
        <strong>{{$_suivi->date|date_format:$conf.date}} à {{$_suivi->date|date_format:$conf.time}}</strong>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_user initials=border}}:<br/>
        <strong>[{{if $_suivi instanceof CObservationMedicale}}Obs{{else}}TC{{/if}}]</strong> {{$_suivi->text}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">
        {{tr}}CTransmissionMedicale.none.importante{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>