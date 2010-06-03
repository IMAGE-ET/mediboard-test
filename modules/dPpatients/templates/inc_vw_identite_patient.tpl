<table class="form">
  <tr>
    <th class="title text" colspan="5">
      {{if $patient->_id_vitale}}
      <div style="float: right;">
        <img src="images/icons/carte_vitale.png" title="Bénéficiaire associé à une carte Vitale" />
      </div>
      {{/if}}

      {{mb_include module=system template=inc_object_idsante400 object=$patient}}
      {{mb_include module=system template=inc_object_history object=$patient}}
      {{mb_include module=system template=inc_object_notes object=$patient}}
      {{$patient}}
    </th>
  </tr>

  <tr>
    <th class="category" colspan="3" style="width: 50%;">
      Identité {{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}}
    </th>
    <th class="category" colspan="2" style="width: 50%;">Coordonnées</th>
  </tr>

  <tr>
    <td rowspan="4" style="width: 0.1%; vertical-align: middle; text-align: center;">
      {{mb_include module=dPpatients template=inc_vw_photo_identite mode="read" size="64"}}
    </td>
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_value object=$patient field="nom"}}</td>
    <th rowspan="2">{{mb_label object=$patient field="adresse"}}</th>
    <td rowspan="2" class="text">{{mb_value object=$patient field="adresse"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_value object=$patient field="prenom"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{mb_value object=$patient field="sexe"}}</td>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>
      {{mb_value object=$patient field="cp"}}
      {{mb_value object=$patient field="ville"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="_age"}}</th>
    <td>{{mb_value object=$patient field="_age"}} ans</td>
    <th>{{mb_label object=$patient field="tel"}}</th>
    <td>{{mb_value object=$patient field="tel"}}</td>
  </tr>

  <tr>
    <th colspan="2">{{mb_label object=$patient field="naissance"}}</th>
    <td>{{mb_value object=$patient field="naissance"}}</td>
    <th>{{mb_label object=$patient field="tel2"}}</th>
    <td>{{mb_value object=$patient field="tel2"}}</td>
  </tr>
  
  <tr>
    <th colspan="2">{{mb_label object=$patient field="nom_jeune_fille"}}</th>
    <td>{{mb_value object=$patient field="nom_jeune_fille"}}</td>
    <th>{{mb_label object=$patient field="tel_autre"}}</th>
    <td>{{mb_value object=$patient field="tel_autre"}}</td>
  </tr>
  
  {{if $patient->medecin_traitant || $patient->_ref_medecins_correspondants|@count}}
  <tr>
    <th class="category" colspan="5">Correspondants médicaux</th>
  </tr>
  
  <tr>
    <td colspan="5" class="text">
      {{assign var=medecin value=$patient->_ref_medecin_traitant}}
      {{if $medecin->_id}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
        <strong>{{$medecin}}</strong> ;
      </span>
      {{/if}}
      {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
        {{assign var=medecin value=$curr_corresp->_ref_medecin}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
          {{$medecin}} ;
        </span>
      {{/foreach}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->rques}}
  <tr>
    <th colspan="5" class="category">{{mb_label object=$patient field="rques"}}</th>
  </tr>
  
  <tr>
    <td colspan="5" class="text">{{mb_value object=$patient field="rques"}}</td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="10">
      <button type="button" class="search" onclick="Patient.view('{{$patient->_id}}')">
        Dossier complet
      </button>
      
      <button type="button" class="print" onclick="Patient.print('{{$patient->_id}}')">
        {{tr}}Print{{/tr}}
      </button>
      
      {{if $canPatients->edit}}
        {{if !@$useVitale}}
          {{assign var=useVitale value=0}}
        {{/if}}
        <button type="button" class="edit" onclick="Patient.edit('{{$patient->_id}}', '{{$useVitale}}')">
          {{tr}}Modify{{/tr}}
          {{if $useVitale}}avec Vitale{{/if}}
        </button>
      {{/if}}
      
      {{if $can->admin}} 
      <form name="Purge-{{$patient->_guid}}" action="?m={{$m}}&amp;tab=vw_idx_patients" method="post" onsubmit="return confirmCreation(this)">
        <input type="hidden" name="dosql" value="do_patients_aed" />
        <input type="hidden" name="tab" value="vw_idx_patients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="_purge" value="0" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
              
        <script type="text/javascript">
          confirmPurge = function(form) {
            if (confirm("ATTENTION : Vous êtes sur le point de purger le dossier d'un patient !")) {
              form._purge.value = "1";
              confirmDeletion(form,  {
                typeName:'le patient',
                objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'
              });
            }
          }
        </script>
        <button type="button" class="cancel" onclick="confirmPurge(this.form);">
          {{tr}}Purge{{/tr}}
        </button>
      </form>
      {{/if}}
      
      {{if $app->user_prefs.vCardExport}}
      <button type="button" class="vcard" onclick="Patient.exportVcard('{{$patient->_id}}')">
        {{tr}}Export{{/tr}}
      </button>
      {{/if}}
      
    </td>
  </tr>
</table>