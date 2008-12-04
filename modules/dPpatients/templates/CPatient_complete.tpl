<table class="tbl">
  <tr>
    <th class="title" colspan="2">
    
      <div class="idsante400" id="CPatient-{{$object->_id}}"></div>
     
      <a style="float:right;" href="#nothing" onclick="view_history_patient({{$object->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      
      <a style="float:right;" href="#nothing" onclick="printPatient({{$object->_id}})">
        <img src="images/icons/print.png" alt="imprimer" title="Imprimer la fiche patient" />
      </a>
      
      {{if $can->edit}}
      <a style="float:right;" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$object->_id}}">
        <img src="images/icons/edit.png" alt="modifier" title="Modifier le patient" />
      </a>
      {{/if}}
      
      <div style="float:left;" class="noteDiv CPatient-{{$object->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>

      <form name="actionPat" action="?" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="vw_idx_patients" />
      <input type="hidden" name="patient_id" value="{{$object->_id}}" />
      {{$object->_view}}
      {{if $object->_IPP}}[{{$object->_IPP}}]{{/if}}
      </form>
    </th>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="nom"}}</strong>
      {{mb_value object=$object field="nom"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="adresse"}}</strong>
      {{mb_value object=$object field="adresse"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="prenom"}}</strong>
      {{mb_value object=$object field="prenom"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="cp"}}</strong>
      {{mb_value object=$object field="cp"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="nom_jeune_fille"}}</strong>
      {{mb_value object=$object field="nom_jeune_fille"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="ville"}}</strong>
      {{mb_value object=$object field="ville"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="naissance"}}</strong>
      {{mb_value object=$object field="naissance"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="tel"}}</strong>
      {{mb_value object=$object field="tel"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="sexe"}}</strong>
      {{mb_value object=$object field="sexe"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="tel2"}}</strong>
      {{mb_value object=$object field="tel2"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="nationalite"}}</strong>
      {{mb_value object=$object field="nationalite"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="profession"}}</strong>
      {{mb_value object=$object field="profession"}}
    </td>
  </tr>
  {{if $object->rques}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{mb_label object=$object field="rques"}}</strong>
      {{mb_value object=$object field="rques"}}
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <th class="title" colspan="2">
      Personne à prévenir
    </th>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="prevenir_nom"}}</strong>
      {{mb_value object=$object field="prevenir_nom"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="prevenir_adresse"}}</strong>
      {{mb_value object=$object field="prevenir_adresse"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="prevenir_prenom"}}</strong>
      {{mb_value object=$object field="prevenir_prenom"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="prevenir_cp"}}</strong>
      {{mb_value object=$object field="prevenir_cp"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="prevenir_tel"}}</strong>
      {{mb_value object=$object field="prevenir_tel"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="prevenir_ville"}}</strong>
      {{mb_value object=$object field="prevenir_ville"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="prevenir_parente"}}</strong>
      {{mb_value object=$object field="prevenir_parente"}}
    </td>
    <td class="text" />
  </tr>
  
  <tr>
    <th class="title" colspan="2">
      Informations médicales
    </th>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="matricule"}}</strong>
      {{mb_value object=$object field="matricule"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="cmu"}}</strong>
      
        {{if $object->cmu}}
          {{if $object->fin_amo}}
          jusqu'au 
          {{mb_value object=$object field="fin_amo"}}
          {{else}}
          Oui
          {{/if}}
        {{else}}
          Non
        {{/if}}
      
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="regime_sante"}}</strong>
      {{mb_value object=$object field="regime_sante"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="notes_amo"}}</strong>
      {{mb_value object=$object field="notes_amo"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="medecin_traitant"}}</strong>
      {{if $object->medecin_traitant}}
        <div class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectView', params: { object_class: 'CMedecin', object_id: {{$object->medecin_traitant}} } });">
          {{mb_value object=$object->_ref_medecin_traitant field="_view"}}
        </div>
      {{/if}}
    </td>
    <td class="text">
      <strong>Correspondants médicaux</strong>
      {{foreach from=$object->_ref_medecins_correspondants item=curr_corresp}}
        <div class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectView', params: { object_class: 'CMedecin', object_id: {{$curr_corresp->medecin_id}} } });">
          {{mb_value object=$curr_corresp->_ref_medecin field="_view"}}
        </div>
      {{foreachelse}}
        <div>{{tr}}CCorrespondant.none{{/tr}}</div>
      {{/foreach}}
    </td>
  </tr>
</table>

<!-- Dossier Médical -->
{{if @$can_view_dossier_medical}}
  {{include file=../../dPpatients/templates/CDossierMedical_complete.tpl object=$object->_ref_dossier_medical}}
{{/if}}