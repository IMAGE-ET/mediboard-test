{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<table class="form">
  <tr>
    <th class="category" colspan="2">Documents</th>
  </tr>
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CFile{{/tr}} - {{tr}}CConsultation{{/tr}}</legend>            
        <div id="files-fdr">
          <script type="text/javascript">
            File.register('{{$consult->_id}}','{{$consult->_class_name}}', 'files-fdr');
          </script>
        </div>
      </fieldset>
  	</td>
  	<td class="halfPane">
      <fieldset>
        <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CConsultation{{/tr}}</legend>            
        <div id="documents-fdr"> 
          {{mb_ternary var=object test=$consult->_is_anesth value=$consult->_ref_consult_anesth other=$consult}}
          {{mb_include_script module="dPcompteRendu" script="document"}}
          <script type="text/javascript">
            Document.register('{{$object->_id}}','{{$object->_class_name}}','{{$consult->_praticien_id}}','documents-fdr');
          </script>
        </div>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{if $conf.dPcabinet.CPrescription.view_prescription}}
      <fieldset>
        <legend>{{tr}}CPrescription{{/tr}}</legend>
        <div id="prescription_register">
          <script type="text/javascript">
            PrescriptionEditor.register('{{$consult->_id}}','{{$consult->_class_name}}','fdr','{{$consult->_praticien_id}}');
          </script>
        </div>
      </fieldset>
      {{/if}}
		</td>  
	</tr>
  {{if $consult->sejour_id}} {{* Cas d'un RPU *}}
  {{assign var=sejour value=$consult->_ref_sejour}}
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CFile{{/tr}} - {{tr}}CSejour{{/tr}}</legend>            
        <div id="files-CSejour">
          <script type="text/javascript">
            File.register('{{$sejour->_id}}','{{$sejour->_class_name}}', 'files-CSejour');
          </script>
        </div>
      </fieldset>
    </td>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CSejour{{/tr}}</legend>            
        <div id="documents-CSejour">
          <script type="text/javascript">
            Document.register('{{$sejour->_id}}','{{$sejour->_class_name}}','{{$sejour->_praticien_id}}','documents-CSejour');
          </script>
        </div>
      </fieldset>
    </td>
  </tr>
  {{/if}}

  <!-- 2eme ligne -->
  {{* si on n'est pas dans le module dPsalleOp (pas besoin du reglement) *}}
  {{if $m!="dPsalleOp"}}
  <tr>
    <th class="category" colspan="2">Règlements</th>
  </tr>
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