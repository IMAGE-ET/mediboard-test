{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<table class="form">
  <tr>
    <th class="category">Fiches et fichiers liés</th>
    <th class="category">Documents</th>
  </tr>
  <tr>
    <!-- 1ere ligne -->
    <td class="text">
      <!-- Fiches d'examens -->
      {{mb_include_script module="dPcabinet" script="exam_dialog"}}
      <script type="text/javascript">
        ExamDialog.register('{{$consult->_id}}','{{$consult->_class_name}}');
      </script>
      
      <hr />
      
      <!-- Fichiers -->
      {{mb_include_script module="dPcabinet" script="file"}}
      <div id="files">
      <script type="text/javascript">
        File.register('{{$consult->_id}}','{{$consult->_class_name}}', 'files');
      </script>
      </div>
    </td>
    
    <td style="width:50%" id="documents-fdr"> 
      {{mb_ternary var=object test=$consult->_is_anesth value=$consult->_ref_consult_anesth other=$consult}}
      
      {{mb_include_script module="dPcompteRendu" script="document"}}
      <script type="text/javascript">
        Document.register('{{$object->_id}}','{{$object->_class_name}}','{{$consult->_praticien_id}}','documents-fdr');
      </script>
     
      {{if $dPconfig.dPcabinet.CPrescription.view_prescription}}
        <hr />
        <div id="prescription_register">
	      <script type="text/javascript">
	        PrescriptionEditor.register('{{$consult->_id}}','{{$consult->_class_name}}','fdr','{{$consult->_praticien_id}}');
	      </script>
	      </div>
      {{/if}}
    </td>
	</tr>
  
  
  <!-- 2eme ligne -->
  {{* si on n'est pas dans le module dPsalleOp (pas besoin du reglement) * }}
  {{if $m!="dPsalleOp"}}
  <tr>
    <!-- Reglement -->
    <td colspan="2">
      {{mb_include_script module="dPcabinet" script="reglement"}}
      <script type="text/javascript">
        Reglement.consultation_id = '{{$consult->_id}}';
        Reglement.user_id = '{{$userSel->_id}}';
        Reglement.register('{{$consult->_id}}');
      </script>
    </td>
  </tr>
  {{/if}}
</table>
