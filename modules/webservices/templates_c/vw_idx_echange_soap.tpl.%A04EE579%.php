<?php /* Smarty version 2.6.18, created on 2009-05-29 16:16:25
         compiled from vw_idx_echange_soap.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'vw_idx_echange_soap.tpl', 16, false),array('function', 'mb_title', 'vw_idx_echange_soap.tpl', 26, false),array('function', 'mb_value', 'vw_idx_echange_soap.tpl', 56, false),array('block', 'tr', 'vw_idx_echange_soap.tpl', 43, false),)), $this); ?>


<script type="text/javascript">

</script>

<table class="main">
  <?php if (! smarty_modifier_cleanField($this->_tpl_vars['echange_soap']->_id)): ?>
  
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="14">ECHANGES SOAP</th>
        </tr>
        <tr>
          <th></th>
          <th><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'echange_soap_id'), $this);?>
</th>
          <th><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'date_echange'), $this);?>
</th>
          <th><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'emetteur'), $this);?>
</th>
          <th><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'destinataire'), $this);?>
</th>
          <th><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'type'), $this);?>
</th>
          <th><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'web_service_name'), $this);?>
</th>
          <th><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'function_name'), $this);?>
</th>
          <th><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'input'), $this);?>
</th>
          <th><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'output'), $this);?>
</th>
        </tr>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['listEchangeSoap']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_echange_soap']):
?>
          <tbody id="echange_<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_echange_soap']->_id); ?>
">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_echange_soap.tpl", 'smarty_include_vars' => array('object' => smarty_modifier_cleanField($this->_tpl_vars['curr_echange_soap']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          </tbody>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="14">
              <?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;do_translation($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>CEchangeHprim.none<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo do_translation($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
            </td>
          </tr>
        <?php endif; unset($_from); ?>
      </table>
    </td>
  </tr>
  <?php else: ?>
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="form">
        <tr>
          <th class="title" colspan="2">
            <?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'function_name'), $this);?>

          </th>
        </tr>
        <tr>
          <th class="category"><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'input'), $this);?>
</th>
          <th class="category"><?php echo smarty_function_mb_title(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'output'), $this);?>
</th>
        </tr>
        <tr>
          <td style="width: 50%">
            <?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'input'), $this);?>

          </td>
          <td>
            <?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['echange_soap']),'field' => 'output'), $this);?>

          </td>
        </tr>
      </table>
    </td>
  </tr>
  <?php endif; ?>
</table>