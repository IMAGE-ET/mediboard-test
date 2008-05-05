{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}


<script type="text/javascript">

function reloadAfterUploadFile(){
  File.refresh('{{$consult->_id}}','{{$consult->_class_name}}');
}

</script>

<table class="form">
  <tr>
    <th class="category">Fiches et fichiers liés</th>
    <th class="category">Documents</th>
  </tr>
  <tr>
    <td class="text">
      
      <!-- Fiches d'examens -->
      {{mb_include_script module="dPcabinet" script="exam_dialog"}}
      <script type="text/javascript">
        ExamDialog.register('{{$consult->_id}}','{{$consult->_class_name}}');
      </script>
      
      <hr />
      
      <!-- Fichiers -->
      {{mb_include_script module="dPcabinet" script="file"}}
      <script type="text/javascript">
        File.register('{{$consult->_id}}','{{$consult->_class_name}}');
      </script>
     
    </td>
    <td style="width:50%"> 
      {{mb_ternary var=object test=$consult->_is_anesth value=$consult->_ref_consult_anesth other=$consult}}
      
      {{mb_include_script module="dPcompteRendu" script="document"}}
      <script type="text/javascript">
        Document.register('{{$object->_id}}','{{$object->_class_name}}','{{$consult->_praticien_id}}','fdr');
      </script>
      
     
      {{if $dPconfig.dPcabinet.CPrescription.view_prescription}}
        <hr />
	      <script type="text/javascript">
	        PrescriptionEditor.register('{{$consult->_id}}','{{$consult->_class_name}}','fdr');
	      </script>
      {{/if}}
    </td>
	</tr>
</table>

<!-- Reglement -->
{{mb_include_script module="dPcabinet" script="reglement"}}

<script type="text/javascript">
  // Initialisations
  Reglement.noReglement = '{{$noReglement}}';
  Reglement.consultation_id = '{{$consult->_id}}';
  Reglement.user_id = '{{$userSel->_id}}';
  Reglement.register('{{$consult->_id}}');
</script>