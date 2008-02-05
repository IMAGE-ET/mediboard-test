{{assign var=codable_id value=$_codable->_id}}
{{if @$entCCAM.$codable_id}}
<div class="error">{{$entCCAM.$codable_id}}</div>
{{else}}
<div class="message">Entête CCAM correctement exporté</div>
{{/if}}
