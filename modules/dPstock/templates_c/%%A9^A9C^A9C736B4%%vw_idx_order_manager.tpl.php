<?php /* Smarty version 2.6.18, created on 2008-03-07 13:54:06
         compiled from vw_idx_order_manager.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'vw_idx_order_manager.tpl', 4, false),array('modifier', 'count', 'vw_idx_order_manager.tpl', 22, false),array('modifier', 'string_format', 'vw_idx_order_manager.tpl', 23, false),array('modifier', 'date_format', 'vw_idx_order_manager.tpl', 53, false),array('modifier', 'JSAttribute', 'vw_idx_order_manager.tpl', 139, false),array('function', 'mb_label', 'vw_idx_order_manager.tpl', 112, false),array('function', 'mb_field', 'vw_idx_order_manager.tpl', 113, false),)), $this); ?>
<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_order_manager&amp;order_id=0">
        Nouvelle commande
      </a>
      <h3>Commandes en attente</h3>
      <table class="tbl" id="waiting_orders">
        <tr>
          <th>Intitulé</th>
          <th>Fournisseur</th>
          <th>Pièces</th>
          <th>Total</th>
          <th>Bloquée</th>
          <th>Actions</th>
        </tr>
        <tbody>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['waiting_orders']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_order']):
?>
          <tr id="order-<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_id); ?>
">
            <td><a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_order_manager&amp;order_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_id); ?>
"><?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_order']->name)): ?><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->name); ?>
<?php else: ?>Sans nom<?php endif; ?></a></td>
            <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_ref_societe->_view); ?>
</td>
            <td><?php echo count(smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_ref_order_items)); ?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_total))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
            <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->locked); ?>
</td>
            <td>mod lock send</td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="8">Aucune commande</td>
          </tr>
        <?php endif; unset($_from); ?>
        </tbody>
      </table>
      
      
      <h3>Commandes en attente de réception</h3>
      <table class="tbl" id="pending_orders">
        <tr>
          <th>Intitulé</th>
          <th>Fournisseur</th>
          <th>Pièces</th>
          <th>Passée le</th>
          <th>Partielle</th>
          <th>Total</th>
          <th>Actions</th>
        </tr>
        <tbody>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['pending_orders']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_order']):
?>
          <tr id="order-<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_id); ?>
">
            <td><a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_order_manager&amp;order_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_id); ?>
"><?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_order']->name)): ?><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->name); ?>
<?php else: ?>Sans nom<?php endif; ?></a></td>
            <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_ref_societe->_view); ?>
</td>
            <td><?php echo count(smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_ref_order_items)); ?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_order']->date_ordered))) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")); ?>
</td>
            <td>O/N</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_total))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
            <td>reçue</td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="8">Aucune commande</td>
          </tr>
        <?php endif; unset($_from); ?>
        </tbody>
      </table>
      
      
      <h3>Anciennes commandes</h3>
      <table class="tbl" id="old_orders">
        <tr>
          <th>Intitulé</th>
          <th>Fournisseur</th>
          <th>Pièces</th>
          <th>Passée le</th>
          <th>Reçue le</th>
          <th>Total</th>
          <th>Actions</th>
        </tr>
        <tbody>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['old_orders']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_order']):
?>
          <tr id="order-<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_id); ?>
">
            <td><a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_order_manager&amp;order_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_id); ?>
"><?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_order']->name)): ?><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->name); ?>
<?php else: ?>Sans nom<?php endif; ?></a></td>
            <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_ref_societe->_view); ?>
</td>
            <td><?php echo count(smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_ref_order_items)); ?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_order']->date_ordered))) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")); ?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_date_received))) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")); ?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_order']->_total))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
            <td>redo del</td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="8">Aucune commande</td>
          </tr>
        <?php endif; unset($_from); ?>
        </tbody>
      </table>
    </td>
    
    <td class="halfPane">
    <form name="edit_order" action="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_order_aed" />
	  <input type="hidden" name="order_id" value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <?php if (smarty_modifier_cleanField($this->_tpl_vars['order']->_id)): ?>
          <th class="title modify" colspan="2">Modification de la commande <?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_view); ?>
</th>
          <?php else: ?>
          <th class="title" colspan="2">Nouvelle commande</th>
          <?php endif; ?>
        </tr>   
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['order']),'field' => 'name'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['order']),'field' => 'name'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['order']),'field' => 'societe_id'), $this);?>
</th>
          <td><select name="societe_id" class="<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_props['societe_id']); ?>
">
            <option value="">&mdash; Choisir une société</option>
            <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_societes']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_societe']):
?>
              <option value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_id); ?>
" <?php if (smarty_modifier_cleanField($this->_tpl_vars['order']->societe_id) == smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_id)): ?> selected="selected" <?php endif; ?> >
              <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_view); ?>

              </option>
            <?php endforeach; endif; unset($_from); ?>
            </select>
          </td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['order']),'field' => 'received'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['order']),'field' => 'received'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['order']),'field' => 'locked'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['order']),'field' => 'locked'), $this);?>
</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            <?php if (smarty_modifier_cleanField($this->_tpl_vars['order']->_id)): ?>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la commande',objName:'<?php echo ((is_array($_tmp=$this->_tpl_vars['order']->_view)) ? $this->_run_mod_handler('JSAttribute', true, $_tmp) : JSAttribute($_tmp)); ?>
'})">Supprimer</button>
            <?php if (! smarty_modifier_cleanField($this->_tpl_vars['order']->locked)): ?>
              <a class="buttonedit" href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_order&amp;order_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
">Peupler</a>
            <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>        
      </table>
    </form>
    </td>
  </tr>
</table>

