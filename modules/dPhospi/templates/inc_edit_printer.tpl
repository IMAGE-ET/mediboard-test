{{if !$printer->_id}}
  {{main}}
    var oForm = getForm("editPrinter");
    $V(oForm.object_class, oForm.object_id.options[oForm.object_id.selectedIndex].getAttribute('object_class'));
  {{/main}}
{{/if}}

{{if $sources|@count}}
  <form name="editPrinter" action="?" onsubmit="return onSubmitFormAjax(this);" method="post">
    {{mb_key object=$printer}}
    <input type="hidden" name="m" value="dPhospi"/>
    <input type="hidden" name="dosql" value="do_printer_aed"/>
    <input type="hidden" name="callback" value="Printer.after_edit_printer"/>
    <input type="hidden" name="del" value="0" />
    <table class="form">
      <tr>
        {{if $printer->_id}}
          <th class="title modify" colspan="2">{{tr}}CPrinter-title-modify{{/tr}}</th>
        {{else}}
          <th class="title create" colspan="2">{{tr}}CPrinter-title-create{{/tr}}</th>
        {{/if}}
      </tr>
      <tr>
        <td>
          {{mb_label object=$printer field=function_id}}
        </td>
        <td>
          <select name="function_id">
            {{foreach from=$functions item=_function}}
              <option value='{{$_function->_id}}'
                {{if $printer->function_id == $_function->_id}}selected = "selected"{{/if}}>{{$_function->_view}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <td>
          {{mb_label object=$printer field=object_class}}
        </td>
        <td>
          <select name="object_id" onchange="$V(this.form.object_class, this.options[this.selectedIndex].getAttribute('object_class'));">
            {{foreach from=$sources item=_source}}
              <option value="{{$_source->_id}}" object_class="{{$_source->_class}}"
                {{if $printer->object_id == $_source->_id && $printer->object_class == $_source->_class}}selected="selected"{{/if}}>
                {{$_source->name}}
              </option>
            {{/foreach}}
          </select>
          <input type="hidden" name="object_class" value="{{$printer->object_class}}"/>
        </td>
      </tr>
      <tr>
        <td colspan="4" style="text-align: center">
          <button class="modify">{{tr}}Save{{/tr}}</button>
          {{if $printer->_id}}
            <button class="cancel" onclick="confirmDeletion(this.form, {
              typeName: 'l\'imprimante',
              objName:'{{$printer->_view|smarty:nodefaults|JSAttribute}}',
              ajax: true})" type="button">{{tr}}Delete{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
    </table>
  </form>
{{else}}
  <div class="info">
    {{tr}}CPrinter.no_sources{{/tr}}
  </div>
{{/if}}