<form name="edit_ressource" method="post" action="?" onsubmit="return false;">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="dosql" value="do_ressource_soin_aed"/>
  <input type="hidden" name="callback" value="Ressource.edit" />
  {{mb_key object=$ressource_soin}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ressource_soin}}

    <tr>
      <th>
        {{mb_label object=$ressource_soin field=code}}
      </th>
      <td>
        {{mb_field object=$ressource_soin field=code}}
      </td>
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
        {{if $ressource_soin->_id}}
				  {{tr}}Modify{{/tr}}  
        {{else}}
				  {{tr}}Create{{/tr}}  
        {{/if}}
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