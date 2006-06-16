<!-- $Id: vw_addedit_planning.tpl 117 2006-06-13 12:54:06Z Rhum1 $ -->

<script type="text/javascript">

function pageMain() {
  incFormOperationMain();
  incFormSejourMain();
}
  
</script>
  
<table class="main">
  {{if $sejour->sejour_id}}
  <tr>
    <td>
      <a class="button" href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;sejour_id=0">
        Programmer un nouveau séjour
      </a>
    </td>
    <td>
      <a class="button" href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id=0&amp;sejour_id={{$sejour->sejour_id}}">
        Programmer une nouvelle intervention dans ce séjour
      </a>
    </td>
  </tr>
  {{/if}}

  <tr>
    {{if $sejour->sejour_id}}
    <th colspan="2" class="title" style="color: #f00;">
      <a style="float:right;" href="javascript:view_log('CSejour',{{$sejour->sejour_id}})">
        <img src="images/history.gif" alt="historique" />
      </a>
      Modification du séjour {{$sejour->_view}}
    </th>
    {{else}}
    <th colspan="2" class="title">      
      Création d'un nouveau séjour
    </th>
    {{/if}}
  </tr>
  
  <tr>
    <td>
      {{include file="inc_form_sejour.tpl"}}
    
    </td>
    <td>
      {{include file="inc_infos_operation.tpl"}}
    </td>
  </tr>

</table>

