{{assign var="patient" value=$object}}

{{mb_include module=dPpatients template=inc_vw_identite_patient tooltip=1}}
  
<table class="form">
  <tr>
    <td colspan="5" class="button">
      {{mb_script module="dPpatients" script="patient" ajax="true"}}
      
      <button type="button" class="search" onclick="Patient.view('{{$patient->_id}}')">
        Dossier Complet
      </button>
      
      <!-- Dossier résumé -->
      <button class="search" onclick="new Url('dPcabinet', 'vw_resume').addParam('patient_id', '{{$patient->_id}}').popup(800, 500, '{{tr}}Summary{{/tr}}');">
        {{tr}}Summary{{/tr}}
      </button>
      
      <button type="button" class="print" onclick="Patient.print('{{$patient->_id}}')">
        {{tr}}Print{{/tr}}
      </button>
      
      {{if $can->edit}}
      <button type="button" class="edit" onclick="Patient.edit('{{$patient->_id}}')">
        {{tr}}Modify{{/tr}}
      </button>
      {{/if}}
      
      {{if $app->user_prefs.vCardExport}}
      <button type="button" class="vcard" onclick="Patient.exportVcard('{{$patient->_id}}')">
        {{tr}}Export{{/tr}}
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
</table>