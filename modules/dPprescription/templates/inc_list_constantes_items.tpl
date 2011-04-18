{{if $element_prescription->_id}}
  <hr />
  <a href="#1" onclick="onSelectConstanteItem('', '{{$element_prescription->_id}}')" class="button new">
    {{tr}}CConstanteItem.create{{/tr}}
  </a>
  <table class="tbl">
    <tr>
      <th colspan="2" class="title">{{tr}}CConstanteItem.all{{/tr}} ({{$element_prescription->_view}})</th>
    </tr>
  </table>
  
  <div>
    <table class="tbl"> 
      <tr>
        <th>{{mb_label class=CConstanteItem field=field_constante}}</th>
        <th>{{mb_label class=CConstanteItem field=commentaire}}</th>
      </tr>
      {{foreach from=$constantes_items item=_constante_item}}
        <tr {{if $constante_item_id == $_constante_item->_id}}class="selected"{{/if}}>
          <td>
             <a href="#1" onclick="onSelectConstanteItem('{{$_constante_item->_id}}','{{$element_prescription->_id}}', this.up('tr'))">
              {{mb_value object=$_constante_item field=field_constante}}
             </a>
          </td>
          <td>{{mb_value object=$_constante_item field=commentaire}}</td>
        </tr>
      {{foreachelse}}
      <tr>
        <td colspan="2">
          {{tr}}CConstanteItem.none{{/tr}}
        </td>
      </tr>
      {{/foreach}}  
    </table>
  </div>
{{/if}}