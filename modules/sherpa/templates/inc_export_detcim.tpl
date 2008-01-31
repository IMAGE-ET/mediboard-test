{{assign var=codable_id value=$_codable->_id}}
{{assign var=codable_class value=$_codable->_class_name}}
{{assign var=_sejour value=$_codable->_ref_sejour}}
<tr>
  <th>Diagnostics</th>
  <td colspan="8">
    {{mb_label object=$_sejour field=DP}} : {{$_sejour->DP}}<br />
    {{if $_sejour->DR}}
    {{mb_label object=$_sejour field=DR}} : {{$_sejour->DR}}<br />
    {{/if}}
    
  </td>
  <td>
		{{foreach from=$detCIM.$codable_class.$codable_id item=msg}}
		{{if $msg}}
		<div class="error">{{$msg}}</div>
		{{else}}
		<div class="message">Détail CIM correctement exporté</div>
		{{/if}}
		<br />
		{{/foreach}}
  </td>
</tr>

