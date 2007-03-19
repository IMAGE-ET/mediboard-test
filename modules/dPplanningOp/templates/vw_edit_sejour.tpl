<!-- $Id: vw_addedit_planning.tpl 117 2006-06-13 12:54:06Z Rhum1 $ -->

<script type="text/javascript">

function popCode(type) {
  var url = new Url();
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addElement(document.editSejour.praticien_id, "chir");
  url.addParam("type", type)
  url.popup(600, 500, type);
}

function setCode(sCode, type ) {
  if (!sCode) {
    return;
  }
  
  var oForm = null
  var oField = null;
  
  if (type == "cim10") {
    oForm = document.editSejour;
    oField = oForm.DP;
  }
  
  oField.value = sCode;
}

function pageMain() {
  incFormSejourMain();
}
  
</script>
  
<table class="main">
  {{if $sejour->sejour_id}}
  <tr>
    <td>
      <a class="buttonnew" href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;sejour_id=0">
        Programmer un nouveau séjour
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
      {{if $canSante400->read}}
      <a style="float:right;" href="#" onclick="view_idsante400('CSejour',{{$sejour->sejour_id}})">
        <img src="images/icons/sante400.gif" alt="Sante400" title="Identifiant sante 400"/>
      </a>
      {{/if}}
      <a style="float:right;" href="#" onclick="view_log('CSejour',{{$sejour->sejour_id}})">
        <img src="images/icons/history.gif" alt="historique" />
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

