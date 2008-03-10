<?php /* Smarty version 2.6.18, created on 2008-03-07 15:22:24
         compiled from inc_vw_order.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'inc_vw_order.tpl', 1, false),array('modifier', 'string_format', 'inc_vw_order.tpl', 18, false),)), $this); ?>
<table class="tbl" id="order[<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
]">
  <tr>
    <th colspan="6"><?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_view); ?>
</th>
  </tr>
  <tr>
    <th>Produit</th>
    <th>PU</th>
    <th>Quantité</th>
    <th>Prix</th>
    <th></th>
  </tr>
  <tbody>
  <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['order']->_ref_order_items); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_item']):
?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_vw_order_item.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endforeach; endif; unset($_from); ?>
  </tbody>
  <tr>
    <td colspan="6" id="order[<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
][total]" style="border-top: 1px solid #666; text-align: right;">Total : <?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['order']->_total))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
  </tr>
</table>