<table class="form">
  <tr>
    <th class="title" colspan="2">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$object->_class_name}}', {{$object->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      <div style="float:left;" class="noteDiv {{$object->_class_name}}-{{$object->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th colspan="6" class="title">Liste des examens</th>
        </tr>
        <tr>
          <th>Identifiant</th>
          <th>Libelle</th>
          <th>Type</th>
          <th>Unité</th>
          <th>Min</th>
          <th>Max</th>
        </tr>
        {{foreach from=$object->_ref_examens_labo item="curr_examen"}}
        <tr>
          <td>
            {{$curr_examen->identifiant}}
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{tr}}{{$curr_examen->libelle}}{{/tr}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->type}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->unite}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->min}} {{$curr_examen->unite}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->max}} {{$curr_examen->unite}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>