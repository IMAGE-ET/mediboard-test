<table class="form">
  <tr>
    <th class="title" colspan="2">
    
      <div class="idsante400" id="CPatient-{{$object->_id}}"></div>
     
      <a style="float:right;" href="#nothing" onclick="view_history_patient({{$object->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      
      <div style="float:left;" class="noteDiv CPatient-{{$object->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>

      <form name="actionPat" action="?" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="vw_idx_patients" />
      <input type="hidden" name="patient_id" value="{{$object->_id}}" />
      {{$object->_view}}
      {{if $object->_IPP}}[{{$object->_IPP}}]{{/if}}
      <button type="button" class="print" onclick="printPatient({{$object->_id}})">
        Imprimer
      </button>
      {{if $can->edit}}
      <button type="button" class="modify" onclick="editPatient()">
        Modifier
      </button>
      {{/if}}
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
        <br />Dr. {{mb_value object=$object->_ref_medecin_traitant field="_view"}}<br/>
        {{mb_value object=$object->_ref_medecin_traitant field="adresse"}}
        - {{mb_value object=$object->_ref_medecin_traitant field="cp"}} {{mb_value object=$object->_ref_medecin_traitant field="ville"}}
        {{if $object->_ref_medecin_traitant->tel}}<br />{{mb_value object=$object->_ref_medecin_traitant field="tel"}}{{/if}}
        
      {{/if}}
    </td>
    <td class="text">
      <strong>Médecins correspondants</strong>
      {{if $object->medecin1}}
      
        <br />Dr. {{mb_value object=$object->_ref_medecin1 field="_view"}}
        {{if $object->_ref_medecin1->tel}}
          ({{mb_value object=$object->_ref_medecin1 field="tel"}})
        {{/if}}
      
      {{/if}}
      
      {{if $object->medecin2}}
      
        <br />Dr. {{mb_value object=$object->_ref_medecin2 field="_view"}}
        {{if $object->_ref_medecin2->tel}}
          ({{mb_value object=$object->_ref_medecin2 field="tel"}})
        {{/if}}
      
      {{/if}}
      
      {{if $object->medecin3}}
      
        <br />Dr. {{mb_value object=$object->_ref_medecin3 field="_view"}}
        {{if $object->_ref_medecin3->tel}}
          ({{mb_value object=$object->_ref_medecin3 field="tel"}})
        {{/if}}
      
      {{/if}}
    </td>
  </tr>
</table>

<!-- Dossier Médical -->
{{include file=../../dPpatients/templates/CDossierMedical_complete.tpl object=$object->_ref_dossier_medical}}
