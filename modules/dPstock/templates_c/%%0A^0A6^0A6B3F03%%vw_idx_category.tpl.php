<?php /* Smarty version 2.6.18, created on 2008-03-07 15:49:38
         compiled from vw_idx_category.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'vw_idx_category.tpl', 11, false),array('modifier', 'JSAttribute', 'vw_idx_category.tpl', 43, false),array('function', 'mb_label', 'vw_idx_category.tpl', 36, false),array('function', 'mb_field', 'vw_idx_category.tpl', 37, false),)), $this); ?>
<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="?m=dPstock&amp;tab=vw_idx_category&amp;category_id=0">
        Nouvelle catégorie
      </a>
      <table class="tbl">
        <tr>
          <th>Catégorie</th>
        </tr>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_categories']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_category']):
?>
        <tr <?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_category']->_id) == smarty_modifier_cleanField($this->_tpl_vars['category']->_id)): ?>class="selected"<?php endif; ?>>
          <td class="text">
            <a href="?m=dPstock&amp;tab=vw_idx_category&amp;category_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_category']->_id); ?>
" title="Modifier la catégorie">
              <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_category']->name); ?>

            </a>
          </td>
        </tr>
        <?php endforeach; endif; unset($_from); ?>        
      </table>  
    </td>
    <td class="halfPane">
      <form name="edit_category" action="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_category_aed" />
	  <input type="hidden" name="category_id" value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['category']->_id); ?>
" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <?php if (smarty_modifier_cleanField($this->_tpl_vars['category']->_id)): ?>
          <th class="title modify" colspan="2">Modification de la catégorie <?php echo smarty_modifier_cleanField($this->_tpl_vars['category']->name); ?>
</th>
          <?php else: ?>
          <th class="title" colspan="2">Nouvelle catégorie</th>
          <?php endif; ?>
        </tr> 
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['category']),'field' => 'name'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['category']),'field' => 'name'), $this);?>
</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            <?php if (smarty_modifier_cleanField($this->_tpl_vars['category']->_id)): ?>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la catégorie',objName:'<?php echo ((is_array($_tmp=$this->_tpl_vars['category']->_view)) ? $this->_run_mod_handler('JSAttribute', true, $_tmp) : JSAttribute($_tmp)); ?>
'})">Supprimer</button>
            <?php endif; ?>
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>
</table>