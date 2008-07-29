<table class="main">

  {{if $sejour->_id}}
  <tr>
    <td>
      <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;sejour_id=0">
        {{tr}}CSejour.create{{/tr}}
      </a>
    </td>
    <td>
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id=0&amp;sejour_id={{$sejour->_id}}">
        Programmer une nouvelle intervention dans ce séjour
      </a>
    </td>
  </tr>
  {{/if}}

  <tr>
    {{if $sejour->_id}}
    <th colspan="2" class="title modify">
    
    <div class="idsante400" id="CSejour-{{$sejour->sejour_id}}"></div>
    
    <a style="float:right;" href="#" onclick="view_log('CSejour',{{$sejour->_id}})">
      <img src="images/icons/history.gif" alt="historique" />
    </a>
    <div style="float:left;" class="noteDiv {{$sejour->_class_name}}-{{$sejour->_id}}">
      <img alt="Ecrire une note" src="images/icons/note_grey.png" />
    </div>
      Modification du séjour {{$sejour->_view}} 
      {{if $sejour->_num_dossier}}[{{$sejour->_num_dossier}}]{{/if}}
    </th>
    {{else}}
    <th colspan="2" class="title">      
      Création d'un nouveau séjour
    </th>
    {{/if}}
  </tr>
  
  <tr>
    <td>
      {{include file="js_form_sejour.tpl"}}
      {{include file="inc_form_sejour.tpl" mode_operation=false}}
    </td>
    <td>
      {{include file="inc_infos_operation.tpl"}}
      {{include file="inc_infos_hospitalisation.tpl"}}
    </td>
  </tr>

</table>

