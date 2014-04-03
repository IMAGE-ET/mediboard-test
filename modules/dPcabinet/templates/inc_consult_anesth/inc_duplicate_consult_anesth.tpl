<form name="duplicateConsultAnesth" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_duplicate_dossier_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_consult_anesth_id" value="{{$consult_anesth_id}}"/>
  <input type="hidden" name="operation_id" value="{{$operation->_id}}"/>
  <table class="form">
    <tr>
      <td colspan="2"> <div class="small-info">Une intervention à venir est présente sans dossier d'anesthésie pour ce patient</div></td>
    </tr>
    <tr>
      <td></td>
      <td><strong>{{$operation}}</strong></td>
    </tr>
    <tr>
      <th>{{mb_title object=$operation field=libelle}}</th>
      <td><strong>{{$operation->libelle}}</strong></td>
    </tr>
    <tr>
      <th>{{mb_title object=$operation field=cote}}</th>
      <td><strong>{{mb_value object=$operation field=cote}}</strong></td>
    </tr>
    <tr>
      <th>Prévue le </th>
      <td><strong>{{$operation->_datetime|date_format:$conf.date}}</strong></td>
    </tr>
    <tr>
      <th>Avec le Dr </th>
      <td><strong>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$operation->_ref_chir}}</strong></td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="add" onclick="this.form.submit();">Dupliquer le dossier et associer l'intervention</button>
        <button type="button" class="cancel" onclick="Control.Modal.close();checkConsult();">Ne pas dupliquer</button>
      </td>
    </tr>
  </table>
</form>