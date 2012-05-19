<tr>
  <td class="text">
    <div class="small-info">
      Iconographie utilisable dans les  
      <strong>modèles de documents</strong>.
    </div>
  </td>
</tr>

{{if $object->_id}} 
<tr>
  <th class="title">Portrait</th>
</tr>
<tr>
  <td class="button">
    {{mb_include module=files template=inc_named_file object=$object name=identite.jpg mode=edit}}
  </td>
</tr>

<tr>
  <th class="title">Signature</th>
</tr>
<tr>
  <td class="button">
    {{mb_include module=files template=inc_named_file object=$object name=signature.jpg mode=edit}}
  </td>
</tr>

{{else}}
<tr>
  <td class="text">
    <div class="small-warning">
      Disponible seulement <strong>après la création</strong> de l'utilisateur.
    </div>
  </td>
</tr>
{{/if}}
