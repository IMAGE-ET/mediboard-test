<form name="edit_ressource" method="post" action="?" onsubmit="return false;">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="dosql" value="do_ressource_soin_aed"/>
  <input type="hidden" name="callback" value="Ressource.edit" />
  {{mb_key object=$ressource_soin}}
  
  <table class="form">
    <tr>
      <th colspan="2" class="title {{if $ressource_soin->_id}}modify{{/if}}"">
        {{if !$ressource_soin->_id}}
          {{tr}}CRessourceSoin-title-create{{/tr}}
        {{else}}
        {{tr}}CRessourceSoin-title-modify{{/tr}}
        {{/if}}
      </th>
    </tr>
    <tr>
      <th>
        {{mb_label object=$ressource_soin field=libelle}}
      </th>
      <td>
        {{mb_field object=$ressource_soin field=libelle}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$ressource_soin field=cout}}
      </th>
      <td>
        {{mb_field object=$ressource_soin field=cout}}
      </td>
    </tr>
    <tr>
      <td style="text-align: center" colspan="2">
        <button type="button" class="modify" onclick="Ressource.onSubmit(this.form);">
        {{tr}}Create{{/tr}}  
        </button>
        {{if $ressource_soin->_id}}
          <button type="button" class="trash" onclick="Ressource.confirmDeletion(this.form);">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>