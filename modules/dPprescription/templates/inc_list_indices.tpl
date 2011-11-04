<button type="button" class="new" onclick="refreshFormIndice('', '{{$element_prescription_id}}')">
  {{tr}}CRessourceSoin-new{{/tr}}
</button>

<table class="tbl">
  <tr>
    <th class="title" colspan="2">
      {{tr}}CRessourceSoin-list{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="category">{{mb_label class=CRessourceSoin field=libelle}}</th>
    <th class="category">{{mb_label class=CIndiceCout field=nb}}</th>
  </tr>
  {{foreach from=$indices item=_indice}}
    {{assign var=ressource value=$_indice->_ref_ressource_soin}}
    <tr {{if $_indice->_id == $indice_cout_id}}class="selected"{{/if}}>
      <td>
        <a href="#{{$_indice->_id}}" onclick="onSelectIndice('{{$_indice->_id}}', '{{$element_prescription_id}}', this.up('tr'))">
          {{mb_value object=$ressource field=libelle}}
        </a>
      </td>
      <td>
        {{mb_value object=$_indice field=nb}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">
        {{tr}}CRessourceSoin.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>