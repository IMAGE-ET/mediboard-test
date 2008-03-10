<?php /* Smarty version 2.6.18, created on 2008-03-07 17:25:29
         compiled from inc_vw_order_item.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'inc_vw_order_item.tpl', 1, false),array('modifier', 'string_format', 'inc_vw_order_item.tpl', 3, false),)), $this); ?>
<tr id="order[<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
][<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_item']->_id); ?>
]">
  <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_item']->_view); ?>
</td>
  <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_item']->unit_price))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
  <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_item']->quantity); ?>
</td>
  <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_item']->_price))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
  <td><a href="#1" onclick="actionOrderItem(<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
, 'delete', <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_item']->_id); ?>
)">del</a>
  <a href="#1" onclick="actionOrderItem(<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
, 'inc', <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_item']->_id); ?>
)"> + </a>
  <a href="#1" onclick="actionOrderItem(<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
, 'dec', <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_item']->_id); ?>
)"> - </a></td>
</tr>