<table class="note">
  {{foreach from=$notes item="curr_note"}}
  <tr>
    <th class="info {{$curr_note->degre}}">
      {{$curr_note->date|date_format:"%d/%m/%Y %H:%M"}} &mdash; 
      {{$curr_note->_ref_user->_view}}
      ({{$curr_note->_ref_user->_ref_function->_view}})
    </th>
  </tr>
  <tr>
    <th class="libelle">
      {{$curr_note->libelle}}
    </th>
  </tr>
  <tr>
    <td>
      {{$curr_note->text|nl2br}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td>Pas de note visible</td>
  </tr>
  {{/foreach}}
</table>