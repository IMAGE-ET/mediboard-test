{{mb_script module="dPpatients" script="patient" ajax=true}}

<table class="tbl print_patient" style="page-break-after: always;">
	{{if !@$no_header}}
  <tr>
    <th class="title text" colspan="2">

      {{if $object->date_lecture_vitale}}
        <div style="float: right;">
          <img src="images/icons/carte_vitale.png" title="{{tr}}CPatient-date-lecture-vitale{{/tr}} : {{mb_value object=$object field="date_lecture_vitale" format=relative}}" />
        </div>
      {{/if}}

      {{mb_include module=system template=inc_object_idsante400 object=$object}}
      {{mb_include module=system template=inc_object_history object=$object}}
      
      <a style="float:right;" href="#print-{{$object->_guid}}" onclick="Patient.print('{{$object->_id}}')">
        <img src="images/icons/print.png" alt="imprimer" title="Imprimer la fiche patient" />
      </a>
      
      {{if $can->edit}}
      <a style="float:right;" href="#edit-{{$object->_guid}}" onclick="Patient.edit('{{$object->_id}}')">
        <img src="images/icons/edit.png" alt="modifier" title="Modifier le patient" />
      </a>
      {{/if}}
      
      {{if $app->user_prefs.vCardExport}}
      <a style="float:right;" href="#export-{{$object->_guid}}" onclick="Patient.exportVcard('{{$object->_id}}')">
        <img src="images/icons/vcard.png" alt="export" title="Exporter le patient" />
      </a>
      {{/if}}

      {{mb_include module=system template=inc_object_notes object=$object}}

      <form name="actionPat" action="?" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="vw_idx_patients" />
      <input type="hidden" name="patient_id" value="{{$object->_id}}" />
      {{$object->_view}}
      {{mb_include module=patients template=inc_vw_ipp ipp=$object->_IPP}}
      </form>
    </th>
  </tr>
	{{/if}}
  <tr>
    <td class="button" colspan="2">
      {{assign var=patient value=$object}}
      {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" mode="read"}}
    </td>
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
      {{mb_value object=$object field="prenom"}}{{if $object->prenom_2}}, 
      {{mb_value object=$object field="prenom_2"}}{{/if}}{{if $object->prenom_3}}, 
      {{mb_value object=$object field="prenom_3"}}{{/if}}{{if $object->prenom_4}}, 
      {{mb_value object=$object field="prenom_4"}} {{/if}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="ville"}}</strong>
      {{mb_value object=$object field="cp"}}
      {{mb_value object=$object field="ville"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="nom_jeune_fille"}}</strong>
      {{mb_value object=$object field="nom_jeune_fille"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="tel"}}</strong>
      {{mb_value object=$object field="tel"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="naissance"}}</strong>
      {{mb_value object=$object field="naissance"}}
    </td> 
    <td class="text">
      <strong>{{mb_label object=$object field="tel2"}}</strong>
      {{mb_value object=$object field="tel2"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="sexe"}}</strong>
      {{mb_value object=$object field="sexe"}}
    </td>
      <td class="text">
        <strong>{{mb_label object=$object field="tel_autre"}}</strong>
        {{mb_value object=$object field="tel_autre"}}
    </td>
  </tr>
  <tr>
    <td>
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="email"}}</strong>
      {{mb_value object=$object field="email"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="profession"}}</strong>
      {{mb_value object=$object field="profession"}}
    </td>
    <td class="text">
      <strong>{{mb_label object=$object field="rques"}}</strong>
      {{mb_value object=$object field="rques"}}
    </td>
  </tr>
  
  {{foreach from=$object->_ref_cp_by_relation.prevenir item=prevenir name=foreach_prevenir}}
    <tr>
      <th class="category" colspan="2">
        Personne à prévenir
      </th>
    </tr>
    <tr>
      <td class="text">
        <strong>{{mb_label object=$prevenir field="nom"}}</strong>
        {{mb_value object=$prevenir field="nom"}}
      </td>
      <td class="text">
        <strong>{{mb_label object=$prevenir field="adresse"}}</strong>
        {{mb_value object=$prevenir field="adresse"}}
      </td>
    </tr>
    <tr>
      <td class="text">
        <strong>{{mb_label object=$prevenir field="prenom"}}</strong>
        {{mb_value object=$prevenir field="prenom"}}
      </td>
      <td class="text">
        <strong>{{mb_label object=$prevenir field="cp"}}</strong>
        {{mb_value object=$prevenir field="cp"}}
      </td>
    </tr>
    <tr>
      <td class="text">
        <strong>{{mb_label object=$prevenir field="tel"}}</strong>
        {{mb_value object=$prevenir field="tel"}}
      </td>
      <td class="text">
        <strong>{{mb_label object=$prevenir field="ville"}}</strong>
        {{mb_value object=$prevenir field="ville"}}
      </td>
    </tr>
    <tr>
      <td class="text">
        <strong>{{mb_label object=$prevenir field="parente"}}</strong>
        {{mb_value object=$prevenir field="parente"}}
      </td>
      <td class="text" />
    </tr>
  {{/foreach}}
  {{foreach from=$object->_ref_cp_by_relation.confiance item=confiance}}
    <tr>
      <th class="category" colspan="2">
        Personne de confiance
      </th>
    </tr>
    <tr>
      <td class="text">
        <strong>{{mb_label object=$confiance field="nom"}}</strong>
        {{mb_value object=$confiance field="nom"}}
      </td>
      <td class="text">
        <strong>{{mb_label object=$confiance field="adresse"}}</strong>
        {{mb_value object=$confiance field="adresse"}}
      </td>
    </tr>
    <tr>
      <td class="text">
        <strong>{{mb_label object=$confiance field="prenom"}}</strong>
        {{mb_value object=$confiance field="prenom"}}
      </td>
      <td class="text">
        <strong>{{mb_label object=$confiance field="cp"}}</strong>
        {{mb_value object=$confiance field="cp"}}
      </td>
    </tr>
    <tr>
      <td class="text">
        <strong>{{mb_label object=$confiance field="tel"}}</strong>
        {{mb_value object=$confiance field="tel"}}
      </td>
      <td class="text">
        <strong>{{mb_label object=$confiance field="ville"}}</strong>
        {{mb_value object=$confiance field="ville"}}
      </td>
    </tr>
    <tr>
      <td class="text">
        <strong>{{mb_label object=$confiance field="parente"}}</strong>
        {{mb_value object=$confiance field="parente"}}
      </td>
      <td class="text" />
    </tr>
  {{/foreach}}
  <tr>
    <th class="category" colspan="2">
      Bénéficiaire de soins
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
      <strong>{{mb_label object=$object field="notes_amc"}}</strong>
      {{mb_value object=$object field="notes_amc"}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>{{mb_label object=$object field="medecin_traitant"}}</strong>
      {{assign var=medecin value=$object->_ref_medecin_traitant}}
      {{if $medecin->_id}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
          {{$medecin}}
        </span>
      {{/if}}
    </td>
    <td class="text">
      <strong>Correspondants médicaux</strong>
      {{foreach from=$object->_ref_medecins_correspondants item=curr_corresp}}
	      {{assign var=medecin value=$curr_corresp->_ref_medecin}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
          {{$medecin}}
        </span>
        <br />
      {{foreachelse}}
        <div class="empty">{{tr}}CCorrespondant.none{{/tr}}</div>
      {{/foreach}}
    </td>
  </tr>
  
  <!-- Dossier Médical -->
  {{if $object->_ref_dossier_medical->_canRead && $object->_ref_dossier_medical->_id}}
    <tr>
      <td colspan="2">
        {{mb_include module=patients template=CDossierMedical_complete object=$object->_ref_dossier_medical}}
      </td>
    </tr>
  {{/if}}
  
</table>