<?php /* Smarty version 2.6.13, created on 2006-09-14 16:39:24
         compiled from view_identifiants.tpl */ ?>
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
      <?php if (! $this->_tpl_vars['dialog']): ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_filter_identifiants.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      <?php endif; ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_list_identifiants.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </td>
    <td>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_edit_identifiant.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </td>
  </tr>
</table>

