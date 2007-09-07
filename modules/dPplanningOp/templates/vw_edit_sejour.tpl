<!-- $Id: vw_addedit_planning.tpl 117 2006-06-13 12:54:06Z Rhum1 $ -->

          
          
<script type="text/javascript">

function pageMain() {
  incFormSejourMain();
}
  
</script>
  
<table class="main">

  {{if $sejour->sejour_id}}
  <tr>
    <td>
      <a class="buttonnew" href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;sejour_id=0">
        {{tr}}CSejour.create{{/tr}}
      </a>
    </td>
    <td>
      <a class="buttonnew" href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id=0&amp;sejour_id={{$sejour->sejour_id}}">
        Programmer une nouvelle intervention dans ce séjour
      </a>
    </td>
  </tr>
  {{/if}}

  <tr>
    {{if $sejour->sejour_id}}
    <th colspan="2" class="title modify">
    
    <div class="idsante400" id="CSejour-{{$sejour->sejour_id}}"></div>
    
    <a style="float:right;" href="#" onclick="view_log('CSejour',{{$sejour->sejour_id}})">
      <img src="images/icons/history.gif" alt="historique" />
    </a>
    <div style="float:left;" class="noteDiv {{$sejour->_class_name}}-{{$sejour->_id}}">
      <img alt="Ecrire une note" src="images/icons/note_grey.png" />
    </div>
      Modification du séjour {{$sejour->_view}} {{if $sejour->_num_dossier}}[{{$sejour->_num_dossier}}]{{/if}}
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
      {{assign var="mode_operation" value=false}}
      {{include file="inc_form_sejour.tpl"}}
    </td>
    <td>
      {{include file="inc_infos_operation.tpl"}}
      {{include file="inc_infos_hospitalisation.tpl"}}
    </td>
  </tr>

</table>

