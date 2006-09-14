<script type="text/javascript">

var oForm = null;

function setObject(oObject){
  oForm.object_class.value = oObject.class;
  oForm.object_id.value = oObject.id;
}

function popObject(oElement) {
  oForm = oElement.form;
  var url = new Url;
  url.setModuleAction("system", "object_selector");
  url.addElement(oForm.object_class, "selClass");  
  url.popup(600, 300, "-");
}

</script>

<table class="main">
  <tr>
    <td>
      {{if !$dialog}}
      {{include file="inc_filter_identifiants.tpl"}}
      {{/if}}
      {{include file="inc_list_identifiants.tpl"}}
    </td>
    <td>
      {{include file="inc_edit_identifiant.tpl"}}
    </td>
  </tr>
</table>


