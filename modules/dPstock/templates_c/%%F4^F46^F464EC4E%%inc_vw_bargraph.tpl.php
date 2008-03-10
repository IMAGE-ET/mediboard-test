<?php /* Smarty version 2.6.18, created on 2008-03-07 17:49:25
         compiled from inc_vw_bargraph.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'inc_vw_bargraph.tpl', 1, false),)), $this); ?>
<?php $this->assign('th_max', smarty_modifier_cleanField($this->_tpl_vars['stock']->order_threshold_max/100)); ?>
<?php $this->assign('value', smarty_modifier_cleanField($this->_tpl_vars['stock']->quantity/$this->_tpl_vars['th_max'])); ?>
<?php $this->assign('th_critical', smarty_modifier_cleanField($this->_tpl_vars['stock']->order_threshold_critical/$this->_tpl_vars['th_max'])); ?>
<?php $this->assign('th_min', smarty_modifier_cleanField($this->_tpl_vars['stock']->order_threshold_min/$this->_tpl_vars['th_max']-$this->_tpl_vars['th_critical'])); ?>
<?php $this->assign('th_optimum', smarty_modifier_cleanField($this->_tpl_vars['stock']->order_threshold_optimum/$this->_tpl_vars['th_max']-$this->_tpl_vars['th_critical']-$this->_tpl_vars['th_min'])); ?>

<div style="background-color: #FFFFFF; height: 10px; min-width: 50px;">
  <div style="overflow: hidden; height: 70%" title="En stock : <?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->quantity); ?>
">
    <div style="width: <?php echo smarty_modifier_cleanField($this->_tpl_vars['value']); ?>
%; background: #888; height:100%;"></div>
  </div>
  <div style="background: #CCC; height: 30%" title="Maximum : <?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->order_threshold_max); ?>
">
    <div style="width: <?php echo smarty_modifier_cleanField($this->_tpl_vars['th_critical']); ?>
%; background: #F66; float:left; height:100%;" title="Critique : <?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->order_threshold_critical); ?>
"></div>
    <div style="width: <?php echo smarty_modifier_cleanField($this->_tpl_vars['th_min']); ?>
%; background: #FE6; float:left; height:100%;" title="Minimum : <?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->order_threshold_min); ?>
"></div>
    <div style="width: <?php echo smarty_modifier_cleanField($this->_tpl_vars['th_optimum']); ?>
%; background: #66ff99; float:left; height:100%;" title="Optimal : <?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->order_threshold_optimum); ?>
"></div>
  </div>
</div>