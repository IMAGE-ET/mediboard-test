{{assign var=dossier_medical_patient value=$patient->_ref_dossier_medical}}
{{assign var=dossier_medical_sejour value=$sejour->_ref_dossier_medical}}
<table class="form">
	<tr>
		<th class="category">Facteur de risque</th>
		<th class="category">Patient</th>
		<th class="category">Chirurgie</th>
	</tr>
	<tr>
	  <th>Maladie thrombo-embolique</th>
		<td style="text-align: center;">
		  <form name="editThromboPatient" method="post" action="?">
		    <input type="hidden" name="m" value="dPpatients" />
				<input type="hidden" name="dosql" value="do_dossierMedical_aed" />
				<input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$patient->_id}}" />
        <input type="hidden" name="object_class" value="CPatient" />
				{{mb_field object=$dossier_medical_patient field="risque_thrombo_patient" onchange="submitFormAjax(this.form, 'systemMsg');"}}
		  </form>
		</td>
		
		{{if $sejour->_id}}
		<td style="text-align: center;">
		  <form name="editThromboChir" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="object_class" value="CSejour" />
				{{mb_field object=$dossier_medical_sejour field="risque_thrombo_chirurgie" onchange="submitFormAjax(this.form, 'systemMsg');"}}
		  </form>
		</td>	
		{{else}}
		<td rowspan="4">
			<div class="small-info">
				Aucun séjour n'est associé à cette consultation
			</div>
		</td>
		{{/if}}
	</tr>
  <tr>
    <th>MCJ</th>
    <td style="text-align: center;">
		  <form name="editMCJPatient" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$patient->_id}}" />
        <input type="hidden" name="object_class" value="CPatient" />
				{{mb_field object=$dossier_medical_patient field="risque_MCJ_patient" onchange="submitFormAjax(this.form, 'systemMsg');"}}
		  </form>
		</td>
		{{if $sejour->_id}}
    <td style="text-align: center;">
		  <form name="editMCJPatient" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="object_class" value="CSejour" />
				{{mb_field object=$dossier_medical_sejour field="risque_MCJ_chirurgie" onchange="submitFormAjax(this.form, 'systemMsg');"}}
			</form>	
	  </td> 
		{{/if}}
  </tr>
  <tr>
    <th>Risque Anesthesique - Antibioprophylaxie</th>
    <td style="text-align: center;">&mdash;</td>
		{{if $sejour->_id}}
    <td style="text-align: center;">
		  <form name="editAntibioSejour" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="object_class" value="CSejour" />
				{{mb_field object=$dossier_medical_sejour field="risque_antibioprophylaxie" onchange="submitFormAjax(this.form, 'systemMsg');"}}
			</form>
		</td> 
		{{/if}}
  </tr>
	<tr>
    <th>Risque Anesthesique - Prophylaxie</th>
    <td style="text-align: center;">&mdash;</td>
		{{if $sejour->_id}}
    <td style="text-align: center;">
		  <form name="editProphylaxieSejour" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="object_class" value="CSejour" />
				{{mb_field object=$dossier_medical_sejour field="risque_prophylaxie" onchange="submitFormAjax(this.form, 'systemMsg');"}}
		  </form>
	 </td>  
	 {{/if}}
  </tr>
</table>