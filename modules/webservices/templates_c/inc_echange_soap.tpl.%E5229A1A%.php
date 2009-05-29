<?php /* Smarty version 2.6.18, created on 2009-05-29 16:58:50
         compiled from inc_echange_soap.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'inc_echange_soap.tpl', 13, false),array('modifier', 'str_pad', 'inc_echange_soap.tpl', 21, false),array('function', 'mb_value', 'inc_echange_soap.tpl', 26, false),)), $this); ?>


<tr>
  <td>
   <?php if (smarty_modifier_cleanField($this->_tpl_vars['object']->_self_emetteur)): ?>
     <img src="images/icons/prev.png" alt="&lt;" />
   <?php else: ?>
     <img src="images/icons/next.png" alt="&gt;" />
   <?php endif; ?>
  </td>
  <td>
    <a href="?m=webservices&amp;tab=vw_idx_echange_soap&amp;echange_soap_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['object']->_id); ?>
" class="button search">
     <?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['object']->echange_soap_id))) ? $this->_run_mod_handler('str_pad', true, $_tmp, 6, '0', 'STR_PAD_LEFT') : str_pad($_tmp, 6, '0', 'STR_PAD_LEFT')); ?>

    </a>
  </td>
  <td>
    <span>
      <label title='<?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['object']),'field' => 'date_echange'), $this);?>
'>
        <?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['object']),'field' => 'date_echange','format' => 'relative'), $this);?>

      </label>
    </span>
  </td>
  <td>
    <?php if (smarty_modifier_cleanField($this->_tpl_vars['object']->_self_emetteur)): ?>
     <label title='<?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['object']),'field' => 'emetteur'), $this);?>
' style="font-weight:bold">
       [SELF]
     </label>
     <?php else: ?>
       <?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['object']),'field' => 'emetteur'), $this);?>

     <?php endif; ?>
  </td>
  <td>
    <?php if (smarty_modifier_cleanField($this->_tpl_vars['object']->_self_destinataire)): ?>
     <label title='<?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['object']),'field' => 'destinataire'), $this);?>
' style="font-weight:bold">
       [SELF]
     </label>
     <?php else: ?>
      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '<?php echo smarty_modifier_cleanField($this->_tpl_vars['object']->_guid); ?>
');">
         <?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['object']),'field' => 'destinataire'), $this);?>

       </span>
     <?php endif; ?>
  </td>
  <td><?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['object']),'field' => 'type'), $this);?>
</td>
  <td><?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['object']),'field' => 'web_service_name'), $this);?>
</td>
  <td><?php echo smarty_function_mb_value(array('object' => smarty_modifier_cleanField($this->_tpl_vars['object']),'field' => 'function_name'), $this);?>
</td>
  <td><?php if (smarty_modifier_cleanField($this->_tpl_vars['object']->input)): ?>Oui<?php else: ?>Non<?php endif; ?></td>
  <td><?php if (smarty_modifier_cleanField($this->_tpl_vars['object']->output)): ?>Oui<?php else: ?>Non<?php endif; ?></td>
</tr>