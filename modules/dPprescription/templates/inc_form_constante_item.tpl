<form name="editConstanteItem" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_constante_item_aed" />
  <input type="hidden" name="callback" value="refreshListConstantesItems" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$constante_item}}
  {{mb_field object=$constante_item field=category_prescription_id hidden=true}}
  <table class="form">
    <tr>
       {{if $constante_item->_id}}
         <th class="title text modify" colspan="2">
           {{tr}}CConstanteItem-title-modify{{/tr}} ({{$constante_item->_ref_category_prescription->_view}})
         </th>
       {{else}}
         <th class="title text" colspan="2">
           {{tr}}CConstanteItem-title-create{{/tr}} ({{$constante_item->_ref_category_prescription->_view}})
         </th>
       {{/if}}
      </tr>
      <tr>
        <th>{{mb_label object=$constante_item field="field_constante"}}</th>
        <td>
          {{mb_field object=$constante_item field=field_constante class="autocomplete"}}
        </td>
      </tr>
      <tr>
       <th>{{mb_label object=$constante_item field="commentaire"}}</th>
       <td>{{mb_field object=$constante_item field="commentaire"}}</td>
     </tr>
     <tr>
      <td class="button" colspan="2">
        <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
        {{if $constante_item->_id}}
        <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{ ajax: true, typeName:'le champ de constante',objName:'{{$constante_item->field_constante|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
        {{/if}}
      </td>
  </table>
</form>