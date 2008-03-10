<?php /* Smarty version 2.6.18, created on 2008-03-07 13:56:33
         compiled from vw_idx_product.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cleanField', 'vw_idx_product.tpl', 10, false),array('modifier', 'nl2br', 'vw_idx_product.tpl', 29, false),array('modifier', 'JSAttribute', 'vw_idx_product.tpl', 94, false),array('modifier', 'string_format', 'vw_idx_product.tpl', 153, false),array('function', 'mb_label', 'vw_idx_product.tpl', 55, false),array('function', 'mb_field', 'vw_idx_product.tpl', 56, false),)), $this); ?>
<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="tab" value="vw_idx_product" />
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
&amp;tab=vw_idx_product&amp;product_id=0">
        Créer un nouveau produit
      </a>
    <?php if (smarty_modifier_cleanField($this->_tpl_vars['category']->category_id)): ?>
    <h2><?php echo smarty_modifier_cleanField($this->_tpl_vars['category']->_view); ?>
</h2>
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>Description</th>
          <th>Code barre</th>
        </tr>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['category']->_ref_products); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_product']):
?>
          <tr <?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id) == smarty_modifier_cleanField($this->_tpl_vars['product']->_id)): ?>class="selected"<?php endif; ?>>
            <td><a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_product&amp;product_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id); ?>
" title="Voir ou modifier le produit"><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->name); ?>
</a></td>
            <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_product']->description))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
            <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->barcode); ?>
</td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="3">Aucun produit dans cette catégorie</td>
          </tr>
        <?php endif; unset($_from); ?>
      </table>
    <?php endif; ?>
      
    </td>
    <td class="halfPane">
      <form name="edit_product" action="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_product_aed" />
	  <input type="hidden" name="product_id" value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['product']->_id); ?>
" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <?php if (smarty_modifier_cleanField($this->_tpl_vars['product']->_id)): ?>
          <th class="title modify" colspan="2">Modification de la fiche <?php echo smarty_modifier_cleanField($this->_tpl_vars['product']->_view); ?>
</th>
          <?php else: ?>
          <th class="title" colspan="2">Création d'une fiche</th>
          <?php endif; ?>
        </tr>   
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['product']),'field' => 'name'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['product']),'field' => 'name'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['product']),'field' => 'category_id'), $this);?>
</th>
          <td><select name="category_id" class="<?php echo smarty_modifier_cleanField($this->_tpl_vars['product']->_props['category_id']); ?>
">
            <option value="">&mdash; Choisir une catégorie</option>
            <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_categories']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_category']):
?>
              <option value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_category']->_id); ?>
" <?php if (smarty_modifier_cleanField($this->_tpl_vars['product']->category_id) == smarty_modifier_cleanField($this->_tpl_vars['curr_category']->_id)): ?> selected="selected" <?php endif; ?> >
              <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_category']->_view); ?>

              </option>
            <?php endforeach; endif; unset($_from); ?>
            </select>
          </td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['product']),'field' => 'societe_id'), $this);?>
</th>
          <td><select name="societe_id" class="<?php echo smarty_modifier_cleanField($this->_tpl_vars['product']->_props['societe_id']); ?>
">
            <option value="">&mdash; Choisir un fabricant</option>
            <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_societes']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_societe']):
?>
              <option value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_id); ?>
" <?php if (smarty_modifier_cleanField($this->_tpl_vars['product']->societe_id) == smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_id)): ?> selected="selected" <?php endif; ?> >
              <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_view); ?>

              </option>
            <?php endforeach; endif; unset($_from); ?>
            </select>
          </td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['product']),'field' => 'barcode'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['product']),'field' => 'barcode'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['product']),'field' => 'description'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['product']),'field' => 'description'), $this);?>
</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            <?php if (smarty_modifier_cleanField($this->_tpl_vars['product']->_id)): ?>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le produit',objName:'<?php echo ((is_array($_tmp=$this->_tpl_vars['product']->_view)) ? $this->_run_mod_handler('JSAttribute', true, $_tmp) : JSAttribute($_tmp)); ?>
'})">Supprimer</button>
            <?php endif; ?>
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
  <?php if (smarty_modifier_cleanField($this->_tpl_vars['product']->_id)): ?>
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="title" colspan="3">Stock(s) correspondant(s)</th>
        </tr>
        <tr>
          <th>Groupe</th>
          <th>En stock</th>
          <th>Seuils de Commande</th>
        </tr>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['product']->_ref_stocks); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_stock']):
?>
        <tr>
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
          <td colspan="3">Aucun stock trouvé</td>
        </tr>
        <?php endif; unset($_from); ?>
        <?php if (smarty_modifier_cleanField($this->_tpl_vars['product']->_id)): ?>
          <tr>
            <td colspan="3">
              <button class="new" type="button" onclick="window.location='?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_stock&amp;stock_id=0&amp;product_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['product']->_id); ?>
'">
                Nouveau stock pour ce produit
              </button>
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Référence(s) correspondante(s)</th>
        </tr>
        <tr>
           <th>Fournisseur</th>
           <th>Quantité</th>
           <th>Prix</th>
           <th>Prix Unitaire</th>
         </tr>
         <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['product']->_ref_references); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_reference']):
?>
         <tr>
           <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_ref_societe->_view); ?>
</td>
           <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->quantity); ?>
</td>
           <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->price))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
           <td><?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_unit_price))) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.2f") : smarty_modifier_string_format($_tmp, "%.2f")); ?>
</td>
         </tr>
         <?php endforeach; else: ?>
         <tr>
           <td colspan="4">Aucune référence trouvée</td>
         </tr>
         <?php endif; unset($_from); ?>
         <?php if (smarty_modifier_cleanField($this->_tpl_vars['product']->_id)): ?>
          <tr>
            <td colspan="4">
              <button class="new" type="button" onclick="window.location='?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_reference&amp;reference_id=0&amp;product_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['product']->_id); ?>
'">
                Nouvelle référence pour ce produit
              </button>
            </td>
          </tr>
        <?php endif; ?>
       </table>
    
    </td>
  </tr>
  <?php endif; ?>
</table>