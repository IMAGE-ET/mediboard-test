<tr>
  <th>Actes</th>
  <td class="text">
    <!-- Gestion des codes -->
    {{include file="../../dPsalleOp/templates/inc_manage_codes.tpl"}}
  </td>
</tr>
<tr>
  <th>{{tr}}{{$subject->_class_name}}{{/tr}}
  {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
  <br />
  Côté {{tr}}COperation.cote.{{$subject->cote}}{{/tr}}
  <br />
  ({{$subject->temp_operation|date_format:"%Hh%M"}})
  {{/if}}
  </th>
  <td class="text">
    <!-- Codage des actes -->
    {{include file="../../dPsalleOp/templates/inc_codage_actes.tpl"}}
  </td>
</tr>