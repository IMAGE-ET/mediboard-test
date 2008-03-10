<?php /* Smarty version 2.6.18, created on 2008-03-07 15:49:46
         compiled from vw_idx_reference.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'vw_idx_reference.tpl', 15, false),array('modifier', 'count', 'vw_idx_reference.tpl', 41, false),array('modifier', 'string_format', 'vw_idx_reference.tpl', 51, false),array('modifier', 'nl2br', 'vw_idx_reference.tpl', 104, false),array('modifier', 'JSAttribute', 'vw_idx_reference.tpl', 119, false),array('function', 'mb_label', 'vw_idx_reference.tpl', 87, false),array('function', 'mb_field', 'vw_idx_reference.tpl', 109, false),)), $this); ?>
<script type="text/javascript">
function pageMain() {
  PairEffect.initGroup("productToggle", { bStartVisible: true });
}
</script>
<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="dPreference" />
        <input type="hidden" name="tab" value="vw_idx_reference" />
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
&amp;tab=vw_idx_reference&amp;reference_id=0">
        Nouvelle réference
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
        </tr>
        
        <!-- Products list -->
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['category']->_ref_products); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_product']):
?>
        <tr id="product-<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id); ?>
-trigger">
          <td colspan="4">
            <a style="display: inline; float: right; font-weight: normal;" href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_reference&amp;reference_id=0&amp;product_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id); ?>
">
              Nouvelle référence
            </a>
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
          <tr <?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_id) == smarty_modifier_cleanField($this->_tpl_vars['reference']->_id)): ?>class="selected"<?php endif; ?>>
            <td><a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_reference&amp;reference_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_id); ?>
" title="Voir ou modifier la référence"><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_ref_societe->_view); ?>
</a></td>
            <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->quantity); ?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->price))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_unit_price))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="4">Aucune réference pour ce produit</td>
          </tr>
        <?php endif; unset($_from); ?>
        </tbody>
      <?php endforeach; else: ?>
        <tr>
          <td colspan="4">Aucun produit dans cette catégorie</td>
        </tr>
      <?php endif; unset($_from); ?>
      </table>
    <?php endif; ?>
    </td>


    <td class="halfPane">
      <?php if (smarty_modifier_cleanField($this->_tpl_vars['can']->edit) && smarty_modifier_cleanField($this->_tpl_vars['reference']->product_id) || smarty_modifier_cleanField($this->_tpl_vars['reference']->societe_id)): ?>
      <form name="edit_reference" action="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_reference_aed" />
	  <input type="hidden" name="reference_id" value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['reference']->_id); ?>
" />
      <?php if (smarty_modifier_cleanField($this->_tpl_vars['reference']->product_id)): ?><input type="hidden" name="product_id" value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['reference']->product_id); ?>
" /><?php endif; ?>
      <?php if (smarty_modifier_cleanField($this->_tpl_vars['reference']->societe_id)): ?><input type="hidden" name="societe_id" value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['reference']->societe_id); ?>
" /><?php endif; ?>
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <?php if (smarty_modifier_cleanField($this->_tpl_vars['reference']->_id)): ?>
          <th class="title modify" colspan="2">Modification de la référence <?php echo smarty_modifier_cleanField($this->_tpl_vars['reference']->_view); ?>
</th>
          <?php else: ?>
          <th class="title" colspan="2">Création d'une référence</th>
          <?php endif; ?>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['reference']),'field' => 'societe_id'), $this);?>
</th>
          <td><select name="societe_id" class="<?php echo smarty_modifier_cleanField($this->_tpl_vars['reference']->_props['societe_id']); ?>
">
            <option value="">&mdash; Choisir un Fournisseur</option>
            <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_societes']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_societe']):
?>
              <option value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->societe_id); ?>
" <?php if (smarty_modifier_cleanField($this->_tpl_vars['reference']->societe_id) == smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_id)): ?> selected="selected" <?php endif; ?> >
              <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_view); ?>

              </option>
            <?php endforeach; endif; unset($_from); ?>
            </select>
          </td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['reference']),'field' => 'product_id'), $this);?>
</th>
          <td>
            <a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_product&amp;product_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['reference']->_ref_product->_id); ?>
" title="Voir ou modifier le produit">
              <b><?php echo smarty_modifier_cleanField($this->_tpl_vars['reference']->_ref_product->_view); ?>
</b>
            </a><br />
            <?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['reference']->_ref_product->description))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>

          </td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['reference']),'field' => 'quantity'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['reference']),'field' => 'quantity'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['reference']),'field' => 'price'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['reference']),'field' => 'price'), $this);?>
</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            <?php if (smarty_modifier_cleanField($this->_tpl_vars['reference']->_id)): ?>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la référence',objName:'<?php echo ((is_array($_tmp=$this->_tpl_vars['reference']->_view)) ? $this->_run_mod_handler('JSAttribute', true, $_tmp) : JSAttribute($_tmp)); ?>
'})">Supprimer</button>
            <?php endif; ?>
          </td>
        </tr>        
      </table>
      </form>
      <?php endif; ?>
    </td>
  </tr>
</table>