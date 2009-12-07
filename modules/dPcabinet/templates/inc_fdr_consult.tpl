{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<table style="width: 100%">
  <!-- 1ere ligne -->
  <tr>
    <td style="width: 50%">

<table class="form">
  <!-- Fichiers -->
	<tr>
		<th class="category">{{tr}}CFile{{/tr}}</th>
	</tr>
	
	<tr>
		<td>
      {{mb_include_script module="dPcabinet" script="file"}}
      <div id="files">
      <script type="text/javascript">
        File.register('{{$consult->_id}}','{{$consult->_class_name}}', 'files');
      </script>
      </div>
    </td>
  </tr>
  
</table>
      
  	</td>
  	<td style="width: 50%">

<table class="form">
  <tr>
    <th class="category">Documents</th>
  </tr>

	<tr>
    <td id="documents-fdr"> 
      {{mb_ternary var=object test=$consult->_is_anesth value=$consult->_ref_consult_anesth other=$consult}}
      
      {{mb_include_script module="dPcompteRendu" script="document"}}
      <script type="text/javascript">
        Document.register('{{$object->_id}}','{{$object->_class_name}}','{{$consult->_praticien_id}}','documents-fdr');
      </script>
    </td>
	</tr>
     
  {{if $dPconfig.dPcabinet.CPrescription.view_prescription && !$consult->sejour_id}}
  <tr>
    <th class="category">{{tr}}CPrescription{{/tr}}</th>
  </tr>

	<tr>
		<td id="prescription_register">
      <script type="text/javascript">
       PrescriptionEditor.register('{{$consult->_id}}','{{$consult->_class_name}}','fdr','{{$consult->_praticien_id}}');
     </script>
		</td>
	</tr>
  {{/if}}
</table>

		</td>  
	<tr>

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
	{{if array_key_exists("sigems", $modules)}}
    <!-- Inclusion de la gestion du système de facturation -->
    {{mb_include module=sigems template=check_actes_reels}}
  {{/if}}
</table>


