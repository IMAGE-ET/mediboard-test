{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{if "dmp"|module_active}}
  {{mb_script module="dmp" script="cdmp" ajax="true"}}
{{/if}}

{{assign var="patient" value=$object}}
<table class="tbl tooltip">
  <tr>
    <th class="title text" colspan="3">
      {{mb_include module=dPpatients template=inc_view_ins_patient patient=$patient}}

      {{mb_include module=system template=inc_object_idsante400 object=$patient}}
      {{mb_include module=system template=inc_object_history object=$patient}}
      {{mb_include module=system template=inc_object_notes object=$patient}}
      {{$patient}}
      {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
    </th>
  </tr>
  <tr>
    <td rowspan="3" style="width: 1px;">
      {{mb_include module=patients template=inc_vw_photo_identite mode=read patient=$patient size=50}}
    </td>
    <td>{{mb_value object=$patient}}</td>
    <td>{{mb_value object=$patient field="adresse"}}</td>
  </tr>
  <tr>
    <td>
      {{mb_value object=$patient field=_age}} ({{mb_value object=$patient field=naissance}})
    </td>
    <td>
      {{mb_value object=$patient field="cp"}}
      {{mb_value object=$patient field="ville"}}
    </td>
  </tr>
  <tr>
    <td colspan="2">
       {{if $conf.ref_pays == 1}}
        {{mb_label object=$patient field="matricule"}} :
        {{mb_value object=$patient field="matricule"}}
      {{else}}
        {{mb_label object=$patient field="avs"}} :
        {{mb_value object=$patient field="avs"}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{mb_label object=$patient field="tel"}} :
      {{mb_value object=$patient field="tel"}}
    </td>
    <td rowspan="3" class="text" style="vertical-align: top;">
      {{mb_label object=$patient field="rques"}}<br />
      {{mb_value object=$patient field="rques"}}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{mb_label object=$patient field="tel2"}} :
      {{mb_value object=$patient field="tel2"}}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{mb_label object=$patient field="tel_autre"}} :
      {{mb_value object=$patient field="tel_autre"}}
    </td>
  </tr>
  <tr>
    <td colspan="3" class="button">
      {{mb_script module="dPpatients" script="patient" ajax="true"}}
      
      <button type="button" class="search" onclick="Patient.view('{{$patient->_id}}')">
        Dossier Complet
      </button>
      
      <!-- Dossier résumé -->
      <button class="search" onclick="Patient.showSummary('{{$patient->_id}}')">
        {{tr}}Summary{{/tr}}
      </button>
      
      <button type="button" class="print" onclick="Patient.print('{{$patient->_id}}')">
        {{tr}}Print{{/tr}}
      </button>
      
      {{if $object->_can->edit}}
        <button type="button" class="edit" onclick="Patient.editModal('{{$patient->_id}}')">
          {{tr}}Modify{{/tr}}
        </button>
      {{/if}}
      
      {{if $app->user_prefs.vCardExport}}
        <button type="button" class="vcard" onclick="Patient.exportVcard('{{$patient->_id}}')">
          {{tr}}Export{{/tr}}
        </button>
      {{/if}}

      {{if "dmp"|module_active}}
        <button type="button" class="dmp-creer" onclick="Cdmp.openDMP('{{$patient->_id}}')">
          {{tr}}DMP{{/tr}}
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
    </td>
  </tr>
  {{if ($patient->medecin_traitant || $patient->_ref_medecins_correspondants|@count)}}
    <tr>
      <th class="category" colspan="3">Correspondants médicaux</th>
    </tr>
    
    <tr>
      <td colspan="5" class="text">
        {{assign var=medecin value=$patient->_ref_medecin_traitant}}
        {{if $medecin->_id}}
          <strong>{{mb_value object=$medecin}}</strong><br />
        {{/if}}
        {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
          {{assign var=medecin value=$curr_corresp->_ref_medecin}}
          {{mb_value object=$medecin}}<br />
        {{/foreach}}
      </td>
    </tr>
  {{/if}}
</table>