{{if $_codable->_ref_actes_ccam|@count}}
{{assign var=codable_id value=$_codable->_id}}
{{assign var=codable_class value=$_codable->_class_name}}
{{assign var=_sejour value=$_codable->_ref_sejour}}
<tr>
  <th>Diagnostics</th>
  <td colspan="8">
    Diagnostic Principal : {{$_sejour->DP}}<br />
    {{if $_sejour->DR}}
    Diagnostic Relié : {{$_sejour->DR}}<br />
    {{/if}}

    {{assign var=dossier_medical value=$_sejour->_ref_dossier_medical}}
    {{if $dossier_medical}}
	    {{foreach from=$dossier_medical->_codes_cim item=code_cim}}
	    Diagnostic associé : {{$code_cim}}<br />
	    {{/foreach}}
    {{/if}}
  </td>
  <td>
		{{foreach from=$detCIM.$codable_class.$codable_id item=msg}}
		{{if $msg}}
		<div class="error">{{$msg}}</div>
		{{else}}
		<div class="message">Détail CIM correctement exporté</div>
		{{/if}}
		{{/foreach}}
  </td>
</tr>
{{/if}}