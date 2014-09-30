<button type="button" class="new" onclick="removeSelected('item'); editItem(0, '{{$item->object_class}}', '{{$item->object_id}}')">Création d'item</button>
<form name="edit_item" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="dosql" value="do_item_prestation_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="afterEditItem" />
  {{mb_key object=$item}}
  {{mb_field object=$item field=object_id hidden=1}}
  {{mb_field object=$item field=object_class hidden=1}}
  {{mb_field object=$item field=rank hidden=1}}
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$item}}
    <tr>
      <th>{{mb_label object=$item field=nom}}</th>
      <td>{{mb_field object=$item field=nom}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$item field=color}}</th>
      <td>{{mb_field object=$item field=color form="edit_item"}}</td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="save" onclick="this.form.onsubmit()">
          {{tr}}{{if $item->_id}}Save{{else}}Create{{/if}}{{/tr}}
        </button>
        {{if $item->_id}}
          <button type="button" class="cancel" onclick="confirmDeletion(this.form, {
              typeName: 'l\'item de prestation',
              objName:'{{$item}}',
              ajax: true})">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
