{{assign var=dossiers_anesth value=$consult->_refs_dossiers_anesth}}

{{mb_default var=onlycreate value=false}}

<script>
  reloadDossierAnesth = function(dossier_anesth_id) {
    var url = new Url("cabinet", "edit_consultation", "tab");
    url.addParam("selConsult", "{{$consult->_id}}");
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.redirect();
  }

  GestionDA = {
    url: null,
    edit: function() {
      var url = new Url("cabinet", "vw_gestion_da");
      url.addParam("conusultation_id", '{{$consult->_id}}');
      url.requestModal(800);
      GestionDA.url = url;
    }
  }
</script>

<table>
  {{assign var=operation value=$consult_anesth->_ref_operation}}
  {{if $consult_anesth->operation_id}}
    {{assign var=sejour value=$consult_anesth->_ref_operation->_ref_sejour}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
      <strong>Séjour :</strong>
      Dr {{$sejour->_ref_praticien->_view}} -
      {{if $sejour->type!="ambu" && $sejour->type!="exte"}} {{$sejour->_duree_prevue}} jour(s) -{{/if}}
      {{mb_value object=$sejour field=type}}
    </span><br/>
    <span onmouseover="ObjectTooltip.createEx(this, '{{$operation->_guid}}', null, { view_tarif: true })">
      <strong>Intervention :</strong>
      le <strong>{{$operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
      par le <strong>Dr {{$operation->_ref_chir->_view}}</strong>
        {{if $operation->libelle}}
          <em>[{{$operation->libelle}}]</em>
        {{/if}}
    </span><br/>
    <strong>{{mb_label object=$operation field="depassement"}} :</strong>
    {{mb_value object=$operation field="depassement"}}
  {{else}}
    {{if $consult_anesth->date_interv || $consult_anesth->chir_id || $consult_anesth->libelle_interv}}
      <tr>
        <th>{{mb_label object=$consult_anesth field=date_interv}}</th>
        <td>{{mb_value object=$consult_anesth field=date_interv}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$consult_anesth field=chir_id}}</th>
        <td>{{mb_value object=$consult_anesth field=chir_id}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$consult_anesth field=libelle_interv}}</th>
        <td>{{mb_value object=$consult_anesth field=libelle_interv}}</td>
      </tr>
    {{/if}}
    <tr>
      <td colspan="2">L'intervention n'est pas liée</td>
    </tr>
  {{/if}}
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="edit" onclick="GestionDA.edit();">
        Gérer le{{if $dossiers_anesth|@count > 1}}s {{$dossiers_anesth|@count}} dossiers {{else}} dossier{{/if}}
      </button>
    </td>
  </tr>
</table>
