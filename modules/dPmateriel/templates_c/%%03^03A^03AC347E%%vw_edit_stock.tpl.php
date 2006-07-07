<?php /* Smarty version 2.6.13, created on 2006-07-05 14:10:10
         compiled from vw_edit_stock.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'vw_edit_stock.tpl', 55, false),)), $this); ?>
<table class="main">
  <tr>
    <td class="tbl">
      <a class="button" href="index.php?m=dPmateriel&amp;tab=vw_edit_stock&amp;stock_id=0">
        Créer un nouveau stock
      </a>  
      <form name="editMat" action="./index.php?m=<?php echo $this->_tpl_vars['m']; ?>
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
          <th class="title" colspan="2">Création d'un stock</th>
          <?php endif; ?>
        </tr>  
        <tr>
          <th><label for="materiel_id" title="Matériel, obligatoire">Matériel</label></th>
          <td><select name="materiel_id" title="<?php echo $this->_tpl_vars['stock']->_props['materiel_id']; ?>
">
            <option value="">&mdash; Choisir un Matériel</option>
            <?php $_from = $this->_tpl_vars['listMateriel']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_materiel']):
?>
              <option value="<?php echo $this->_tpl_vars['curr_materiel']->materiel_id; ?>
" <?php if ($this->_tpl_vars['stock']->materiel_id == $this->_tpl_vars['curr_materiel']->materiel_id): ?> selected="selected" <?php endif; ?> >
              <?php echo $this->_tpl_vars['curr_materiel']->nom; ?>

              </option>
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
          <th><label for="quantite" title="Quantité, obligatoire">Quantité</label></th>
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