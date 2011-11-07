<form name="editIndice" method="post" action="?" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="dosql" value="do_indice_cout_aed" />
  <input type="hidden" name="callback" value="refreshListIndices" />
  {{mb_key object=$indice_cout}}
  {{mb_field object=$indice_cout field=element_prescription_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$indice_cout}}

    <tr>
      <th>
        {{mb_label object=$indice_cout field=nb}}
      </th>
      <td>
        {{mb_field object=$indice_cout field=nb}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$indice_cout field=ressource_soin_id}}
      </th>
      <td>
        <select name="ressource_soin_id" class="notNull">
          <option value="">&mdash; Choisissez une ressource</option>
          {{foreach from=$ressources item=_ressource}}
            <option value="{{$_ressource->_id}}" {{if $_ressource->_id == $indice_cout->ressource_soin_id}}selected="selected"{{/if}}>{{$_ressource->libelle}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <td style="text-align: center" colspan="2">
        <button class="submit">{{tr}}Save{{/tr}}</button>
        {{if $indice_cout->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ ajax: true, typeName:'la ressource',objName:'{{$indice_cout->_ref_ressource_soin->libelle|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
