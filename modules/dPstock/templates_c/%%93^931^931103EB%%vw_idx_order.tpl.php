<?php /* Smarty version 2.6.18, created on 2008-03-07 16:43:57
         compiled from vw_idx_order.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'vw_idx_order.tpl', 5, false),array('modifier', 'count', 'vw_idx_order.tpl', 67, false),array('modifier', 'string_format', 'vw_idx_order.tpl', 77, false),)), $this); ?>
<script type="text/javascript">
function pageMain() {
  regFieldCalendar("edit_order", "date");
  PairEffect.initGroup("productToggle", { bStartVisible: true });
  if(<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
){
    reloadOrder(<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
);
  }
}

function reloadOrder(order_id){
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_order");
  url.addParam("order_id", order_id);
  url.requestUpdate("orders_list", { waitingText: null } );
}

function actionOrderItem(order_id, action, object_id){
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_order");
  url.addParam("order_id", order_id);
  url.addParam("action", action);
  url.addParam("object_id", object_id);
  url.requestUpdate("orders_list", { waitingText: null } );
}
</script>
<table class="main">
  <tr>
    <td class="halfPane">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="tab" value="vw_idx_order" />
        <label for="category_id" title="Choisissez une catégorie">Catégorie</label>
        <select name="category_id" onchange="this.form.submit()">
          <option value="-1" >&mdash; Choisir une catégorie &mdash;</option>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_categories']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_category']):
?> 
          <option value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_category']->category_id); ?>
" <?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_category']->category_id) == smarty_modifier_cleanField($this->_tpl_vars['category']->category_id)): ?>selected="selected"<?php endif; ?>><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_category']->_view); ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
        </select>
        
        <label for="societe_id" title="Choisissez un fournisseur">Fournisseur</label>
        <select name="societe_id" onchange="this.form.submit()">
          <option value="0" >&mdash; Tous les founisseurs &mdash;</option>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_societes']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_societe']):
?> 
          <option value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->societe_id); ?>
" <?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->societe_id) == smarty_modifier_cleanField($this->_tpl_vars['societe']->societe_id)): ?>selected="selected"<?php endif; ?>><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_view); ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
        </select>
      </form>
      <a class="buttonnew" href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_order&amp;order_id=0">
        Nouvelle commande
      </a>

    <?php if (smarty_modifier_cleanField($this->_tpl_vars['category']->category_id)): ?>
    <h3><?php echo smarty_modifier_cleanField($this->_tpl_vars['category']->_view); ?>
</h3>
      <table class="tbl">
        <tr>
          <th>Fournisseur</th>
          <th>Quantité</th>
          <th>Prix</th>
          <th>P.U.</th>
          <th>Actions</th>
        </tr>
        
        <!-- Products list -->
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['category']->_ref_products); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_product']):
?>
        <tr id="product-<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id); ?>
-trigger">
          <td colspan="5">
            <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_view); ?>
 (<?php echo count(smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_ref_references)); ?>
 références)
          </td>
        </tr>
        <tbody class="productToggle" id="product-<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id); ?>
">
        
        <!-- Références list of this Product -->
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_ref_references); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_reference']):
?>
          <tr <?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_id) == smarty_modifier_cleanField($this->_tpl_vars['order']->_id)): ?>class="selected"<?php endif; ?>>
            <td><a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_order&amp;reference_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_id); ?>
" title="Voir ou modifier la référence"><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_ref_societe->_view); ?>
</a></td>
            <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->quantity); ?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->price))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_unit_price))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
            <td><a href="#1" onclick="actionOrderItem(<?php echo smarty_modifier_cleanField($this->_tpl_vars['order']->_id); ?>
, 'add', <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_id); ?>
)">Ajouter</a></td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="5">Aucune réference pour ce produit</td>
          </tr>
        <?php endif; unset($_from); ?>
        </tbody>
      <?php endforeach; else: ?>
        <tr>
          <td colspan="5">Aucun produit dans cette catégorie</td>
        </tr>
      <?php endif; unset($_from); ?>
      </table>
    <?php endif; ?>
    </td>

    <td class="halfPane"><h3>Commandes</h3>
      <div id="orders_list"></div>
    </td>
  </tr>
</table>