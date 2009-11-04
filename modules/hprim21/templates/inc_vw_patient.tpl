<!-- $Id$ -->

<script type="text/javascript">
function view_history_patient(id){
  var url = new Url("dPpatients", "vw_history");
  url.addParam("patient_id", id);
  url.popup(600, 500, "history");
}

function editPatient() {
  var oForm = document.actionPat;
  oForm.elements.tab.value = "vw_edit_patients";
  oForm.submit();
}
</script>

<table class="form">
  <tr>
    <th class="category" colspan="2">
      {{mb_include module=system template=inc_object_idsante400 object=$patient}}
      {{mb_include module=system template=inc_object_history object=$patient}}

      <div style="float:left;" class="noteDiv {{$patient->_guid}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>

      Identité [{{$patient->external_id}}]
    </th>
    <th class="category" colspan="2">Coordonnées</th>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_value object=$patient field="nom"}}</td>
    <th>{{mb_label object=$patient field="adresse1"}}</th>
    <td class="text">
      {{mb_value object=$patient field="adresse1"}}
      {{mb_value object=$patient field="adresse2"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_value object=$patient field="prenom"}}</td>
    <th>{{mb_label object=$patient field="cp"}}</th>
    <td>{{mb_value object=$patient field="cp"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="nom_jeune_fille"}}</th>
    <td>{{mb_value object=$patient field="nom_jeune_fille"}}</td>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>{{mb_value object=$patient field="ville"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="naissance"}}</th>
    <td>{{mb_value object=$patient field="naissance"}}</td>
    <th>{{mb_label object=$patient field="telephone1"}}</th>
    <td>{{mb_value object=$patient field="telephone1"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{mb_value object=$patient field="sexe"}}</td>
    <th>{{mb_label object=$patient field="telephone2"}}</th>
    <td>{{mb_value object=$patient field="telephone2"}}</td>
  </tr>
</table>

<table class="tbl">
  {{if $patient->patient_id}}
  <tr>
    <th colspan="2">Patient interne lié</th>
  </tr>
  <tr>
    <td colspan="2">
      <a href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_ref_patient->_id}}">
        {{$patient->_ref_patient->_view}}
      </a>
    </td>
  </tr>
  {{/if}}
  {{if $patient->_ref_hprim21_sejours|@count}}
  <tr>
    <th colspan="2">Séjours</th>
  </tr>
  <tr>
    <th>Séjour Hprim</th>
    <th>Séjour relié</th>
  </tr>
  {{foreach from=$patient->_ref_hprim21_sejours item=curr_sejour}}
  <tr>
    <td class="text">
      {{$curr_sejour->_view}}
      {{if $curr_sejour->hprim21_medecin_id}}
        Dr {{$curr_sejour->_ref_hprim21_medecin->_view}}
      {{/if}}
	  </td>
    <td class="text">
      {{if $curr_sejour->sejour_id}}
        <a href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->sejour_id}}">
        {{$curr_sejour->_ref_sejour->_view}} - Dr {{$curr_sejour->_ref_sejour->_ref_praticien->_view}}
        </a>
      {{else}}
        Pas de séjour associé
      {{/if}}
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>