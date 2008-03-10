<?php /* Smarty version 2.6.18, created on 2008-03-07 15:49:05
         compiled from vw_idx_stock.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'vw_idx_stock.tpl', 15, false),array('modifier', 'nl2br', 'vw_idx_stock.tpl', 103, false),array('modifier', 'JSAttribute', 'vw_idx_stock.tpl', 126, false),array('function', 'mb_label', 'vw_idx_stock.tpl', 82, false),array('function', 'mb_field', 'vw_idx_stock.tpl', 95, false),)), $this); ?>
<script type="text/javascript">
function pageMain() {
  PairEffect.initGroup("productToggle", { bStartVisible: false });
}
</script>
<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="tab" value="vw_idx_stock" />
        <label for="category_id" title="Choisissez une catégorie">Catégorie</label>
        <select name="category_id" onchange="this.form.submit()">
          <option value="-1" >&mdash; Choisir une catégorie &mdash;</option>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_categories']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_category']):
?> 
          <option value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_category']->category_id); ?>
" <?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_category']->category_id) == smarty_modifier_cleanField($this->_tpl_vars['category']->category_id)): ?>selected="selected"<?php endif; ?>><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_category']->name); ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
        </select>
      </form>
      <a class="buttonnew" href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_stock&amp;stock_id=0">
        Nouveau stock
      </a>

    <?php if (smarty_modifier_cleanField($this->_tpl_vars['category']->category_id)): ?>
    <h3><?php echo smarty_modifier_cleanField($this->_tpl_vars['category']->_view); ?>
</h3>
      <table class="tbl">
        <tr>
          <th>Groupe</th>
          <th>En stock</th>
          <th>Seuils</th>
        </tr>
        
        <!-- Products list -->
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['category']->_ref_products); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_product']):
?>
        <tr id="product-<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id); ?>
-trigger">
          <td colspan="3">
            <a style="display: inline; float: right; font-weight: normal;" href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_stock&amp;stock_id=0&amp;product_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id); ?>
">
              Nouveau stock
            </a>
            <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_view); ?>

          </td>
        </tr>
        <tbody class="productToggle" id="product-<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id); ?>
">
        
        <!-- Stocks list of this Product -->
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_ref_stocks); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_stock']):
?>
          <tr <?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_stock']->_id) == smarty_modifier_cleanField($this->_tpl_vars['stock']->_id)): ?>class="selected"<?php endif; ?>>
            <td><a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_stock&amp;stock_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_stock']->_id); ?>
" title="Voir ou modifier le stock"><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_stock']->_ref_group->_view); ?>
</a></td>
            <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_stock']->quantity); ?>
</td>
            <td><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_vw_bargraph.tpl", 'smarty_include_vars' => array('stock' => smarty_modifier_cleanField($this->_tpl_vars['curr_stock']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="3">Aucun stock pour ce produit</td>
          </tr>
        <?php endif; unset($_from); ?>
        </tbody>
      <?php endforeach; else: ?>
        <tr>
          <td colspan="3">Aucun produit dans cette catégorie</td>
        </tr>
      <?php endif; unset($_from); ?>
      </table>
    <?php endif; ?>
    </td>
    <!-- Edit/New Stock form -->
    <td class="halfPane">
      <form name="edit_stock" action="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_stock_aed" />
      <input type="hidden" name="stock_id" value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->_id); ?>
" />
      <?php if (! smarty_modifier_cleanField($this->_tpl_vars['stock']->_id)): ?><input type="hidden" name="product_id" value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->product_id); ?>
" /><?php endif; ?>
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <?php if (smarty_modifier_cleanField($this->_tpl_vars['stock']->_id)): ?>
          <th class="title modify" colspan="2">Modification du stock de <?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->_view); ?>
</th>
          <?php else: ?>
          <th class="title" colspan="2">Nouveau stock</th>
          <?php endif; ?>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'group_id'), $this);?>
</th>
          <td><select name="group_id" class="<?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->_props['group_id']); ?>
">
            <option value="">&mdash; Choisir un groupe</option>
            <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_groups']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_group']):
?>
              <option value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_group']->_id); ?>
" <?php if (smarty_modifier_cleanField($this->_tpl_vars['stock']->group_id) == smarty_modifier_cleanField($this->_tpl_vars['curr_group']->_id) || ( smarty_modifier_cleanField($this->_tpl_vars['curr_group']->_id) == smarty_modifier_cleanField($this->_tpl_vars['g']) && smarty_modifier_cleanField($this->_tpl_vars['stock']->group_id) != smarty_modifier_cleanField($this->_tpl_vars['curr_group']->_id) )): ?> selected="selected" <?php endif; ?> >
              <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_group']->_view); ?>

              </option>
            <?php endforeach; endif; unset($_from); ?>
            </select>
          </td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'quantity'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'quantity'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'product_id'), $this);?>
</th>
          <td>
            <a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_product&amp;product_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->_ref_product->_id); ?>
" title="Voir ou modifier le produit">
              <b><?php echo smarty_modifier_cleanField($this->_tpl_vars['stock']->_ref_product->_view); ?>
</b>
            </a><br />
            <?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['stock']->_ref_product->description))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>

          </td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'order_threshold_critical'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'order_threshold_critical'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'order_threshold_min'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'order_threshold_min'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'order_threshold_optimum'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'order_threshold_optimum'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'order_threshold_max'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['stock']),'field' => 'order_threshold_max'), $this);?>
</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            <?php if (smarty_modifier_cleanField($this->_tpl_vars['stock']->_id)): ?>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le stock',objName:'<?php echo ((is_array($_tmp=$this->_tpl_vars['stock']->_view)) ? $this->_run_mod_handler('JSAttribute', true, $_tmp) : JSAttribute($_tmp)); ?>
'})">Supprimer</button>
            <?php endif; ?>
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
</table>