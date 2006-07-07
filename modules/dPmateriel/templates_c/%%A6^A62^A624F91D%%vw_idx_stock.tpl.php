<?php /* Smarty version 2.6.13, created on 2006-07-06 14:56:21
         compiled from vw_idx_stock.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'vw_idx_stock.tpl', 90, false),)), $this); ?>
<table class="main">
  <tr>
    <td class="HalfPane">
      <a class="button" href="index.php?m=dPmateriel&amp;tab=vw_idx_stock&amp;stock_id=0">
        Cr�er un nouveau stock
      </a> 
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Mat�riel</th>
          <th>Groupe</th>
          <th>Seuil de Commande</th>
          <th>Quantit�</th>
        </tr>
        <?php $_from = $this->_tpl_vars['listStock']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_stock']):
?>
        <tr>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_stock&amp;stock_id=<?php echo $this->_tpl_vars['curr_stock']->stock_id; ?>
" title="Modifier le stock">
              <?php echo $this->_tpl_vars['curr_stock']->stock_id; ?>

            </a>
          </td>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_materiel&amp;materiel_id=<?php echo $this->_tpl_vars['curr_stock']->_ref_materiel->materiel_id; ?>
" title="Modifier le mat�riel">
              <?php echo $this->_tpl_vars['curr_stock']->_ref_materiel->nom; ?>
 (<?php echo $this->_tpl_vars['curr_stock']->_ref_materiel->_ref_category->category_name; ?>
)
              <?php if ($this->_tpl_vars['curr_stock']->_ref_materiel->code_barre): ?><br /><?php echo $this->_tpl_vars['curr_stock']->_ref_materiel->code_barre;  endif; ?>
              <?php if ($this->_tpl_vars['curr_stock']->_ref_materiel->description): ?><br /><?php echo $this->_tpl_vars['curr_stock']->_ref_materiel->description;  endif; ?>
            </a>
          </td>
          <td><?php echo $this->_tpl_vars['curr_stock']->_ref_group->text; ?>
</td>
          <td><?php echo $this->_tpl_vars['curr_stock']->seuil_cmd; ?>
</td>
          <td><?php echo $this->_tpl_vars['curr_stock']->quantite; ?>
</td>
        </tr>
        <?php endforeach; endif; unset($_from); ?>
      </table>
        
    </td>
    <td class="HalfPane">
      <form name="editStock" action="./index.php?m=<?php echo $this->_tpl_vars['m']; ?>
" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_stock_aed" />  
	  <input type="hidden" name="stock_id" value="<?php echo $this->_tpl_vars['stock']->stock_id; ?>
" />
      <input type="hidden" name="del" value="0" />  
      <table class="form">
        <tr>
          <?php if ($this->_tpl_vars['stock']->stock_id): ?>
          <th class="title" colspan="2" style="color:#f00;">Modification du stock <?php echo $this->_tpl_vars['stock']->_view; ?>
</th>
          <?php else: ?>
          <th class="title" colspan="2">Cr�ation d'un stock</th>
          <?php endif; ?>
        </tr>  
        <tr>
          <th><label for="materiel_id" title="Mat�riel, obligatoire">Mat�riel</label></th>
          <td><select name="materiel_id" title="<?php echo $this->_tpl_vars['stock']->_props['materiel_id']; ?>
">
            <option value="">&mdash; Choisir un Mat�riel</option>
            <?php $_from = $this->_tpl_vars['listCategory']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_cat']):
?>
               <optgroup label="<?php echo $this->_tpl_vars['curr_cat']->category_name; ?>
">
               <?php $_from = $this->_tpl_vars['curr_cat']->_ref_materiel; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_materiel']):
?>
                 <option value="<?php echo $this->_tpl_vars['curr_materiel']->materiel_id; ?>
" <?php if ($this->_tpl_vars['stock']->materiel_id == $this->_tpl_vars['curr_materiel']->materiel_id): ?> selected="selected" <?php endif; ?> >
                 <?php echo $this->_tpl_vars['curr_materiel']->nom; ?>

                 </option>
               <?php endforeach; endif; unset($_from); ?>
               </optgroup>
            <?php endforeach; endif; unset($_from); ?>
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="group_id" title="Groupe, obligatoire">Groupe</label></th>
          <td><select name="group_id" title="<?php echo $this->_tpl_vars['stock']->_props['group_id']; ?>
">
            <option value="">&mdash; Choisir un Groupe</option>
            <?php $_from = $this->_tpl_vars['listGroupes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_groupes']):
?>
              <option value="<?php echo $this->_tpl_vars['curr_groupes']->group_id; ?>
" <?php if ($this->_tpl_vars['stock']->group_id == $this->_tpl_vars['curr_groupes']->group_id): ?> selected="selected" <?php endif; ?> >
              <?php echo $this->_tpl_vars['curr_groupes']->text; ?>

              </option>
            <?php endforeach; endif; unset($_from); ?>
            </select>
          </td>
        </tr>        
        <tr>
          <th><label for="seuil_cmd" title="Seuil de Commande, obligatoire">Seuil de Commande</label></th>
          <td><input name="seuil_cmd" title="<?php echo $this->_tpl_vars['stock']->_props['seuil_cmd']; ?>
" type="text" value="<?php echo $this->_tpl_vars['stock']->seuil_cmd; ?>
" /></td>
        </tr>
        <tr>
          <th><label for="quantite" title="Quantit�, obligatoire">Quantit�</label></th>
          <td><input name="quantite" title="<?php echo $this->_tpl_vars['stock']->_props['quantite']; ?>
" type="text" value="<?php echo $this->_tpl_vars['stock']->quantite; ?>
" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="submit">Valider</button>
            <?php if ($this->_tpl_vars['stock']->stock_id): ?>
              <button type="button" onclick="confirmDeletion(this.form,{typeName:'le stock',objName:'<?php echo ((is_array($_tmp=$this->_tpl_vars['stock']->_view)) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
'})">Supprimer</button>
            <?php endif; ?>
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
</table>