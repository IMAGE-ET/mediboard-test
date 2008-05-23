<table class="tbl">
  <tr>
    <th colspan="3">Historique</th>
  </tr>
  <tr>
    <th>Id</th>
    <th>Ligne</th>
    <th>Signature Prat</th>
  </tr>
  {{foreach from=$parent_lines item=_line_parent}}
  <tr>
    <td>{{$_line_parent->_id}}</td>
    <td>Ligne prévue initialement du {{$_line_parent->debut}} au {{$_line_parent->_fin}}.
    {{if $_line_parent->date_arret}}
    Arrêt le {{$_line_parent->date_arret}}
    {{/if}}</td>
    <td>
    {{if $_line_parent->signee}}
    Oui
    {{else}}
    Non
    {{/if}}
    </td>
  </tr>
  {{/foreach}}
</table>