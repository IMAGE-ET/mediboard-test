<button
  id = "didac_ufs_button_sejour_protocole"
  type="button" 
  class="new" 
  onclick="Modal.open('ufs_modal', { closeOnClick: $('ufs_modal').down('button.tick') } );"
>
  UFs
</button>

<table id="ufs_modal" style="display: none;">
  <tr>
    <th class="category" colspan="2">{{tr}}CUniteFonctionnelle.all{{/tr}}</th>
  </tr>
  <tr>
    <th>
      {{mb_label object=$object field=uf_hebergement_id}}
    </th>
    <td>
      <select name="uf_hebergement_id">
        <option value="">{{tr}}CUniteFonctionnelle.none{{/tr}}</option>
        {{foreach from=$ufs.hebergement item=_uf}}
          <option value="{{$_uf->_id}}" {{if $object->uf_hebergement_id == $_uf->_id}}selected="selected"{{/if}}>
            {{mb_value object=$_uf field=libelle}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  {{if "dPplanningOp CSejour required_uf_soins"|conf:"CGroups-$g" == "no"}}
  <tr>
    <th>
      {{mb_label object=$object field=uf_soins_id}}
    </th>
    <td>
      <select name="uf_soins_id">
        <option value="">{{tr}}CUniteFonctionnelle.none{{/tr}}</option>
        {{foreach from=$ufs.soins item=_uf}}
          <option value="{{$_uf->_id}}" {{if $object->uf_soins_id == $_uf->_id}}selected="selected"{{/if}}>
            {{mb_value object=$_uf field=libelle}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{/if}}

  <tr>
    <th>
      {{mb_label object=$object field=uf_medicale_id}}
    </th>
    <td>
      <select name="uf_medicale_id">
        <option value="">{{tr}}CUniteFonctionnelle.none{{/tr}}</option>
        {{foreach from=$ufs.medicale item=_uf}}
          <option value="{{$_uf->_id}}" {{if $object->uf_medicale_id == $_uf->_id}}selected="selected"{{/if}}>
            {{mb_value object=$_uf field=libelle}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button id="didac_ufs_button_validate" class="tick" type="button">{{tr}}Validate{{/tr}}</button>
    </td>
  </tr>
</table>