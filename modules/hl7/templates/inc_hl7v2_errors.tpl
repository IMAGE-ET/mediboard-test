<table class="main tbl">
  <tr>
    <th>Ligne</th>
    <th>Champ</th>
    <th></th>
    <th></th>
  </tr>
  {{foreach from=$errors item=_error}}
    <tr>
      <td class="narrow">
        {{$_error.line}}
      </td>
      <td class="narrow">
      	{{if $_error.field}}
          {{$_error.field->name}}
				{{/if}}
      </td>
      <td>
        {{if $_error.code|is_numeric}}
          {{tr}}CHL7v2Exception-{{$_error.code}}{{/tr}}
        {{else}}
          {{$_error.code}}
        {{/if}}
      </td>
      <td>
        {{$_error.data}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">Aucune erreur</td>
    </tr>
  {{/foreach}}
</table>