<?php /* Smarty version 2.6.13, created on 2006-07-05 17:13:49
         compiled from idx_stock.tpl */ ?>
<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Matériel</th>
          <th>Groupe</th>
          <th>Seuil de Commande</th>
          <th>Quantité</th>          
        </tr>
        <?php $_from = $this->_tpl_vars['listStock']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_stock']):
?>
        <tr>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_edit_stock&amp;stock_id=<?php echo $this->_tpl_vars['curr_stock']->stock_id; ?>
" title="Modifier le sotck">
              <?php echo $this->_tpl_vars['curr_stock']->stock_id; ?>

            </a>
          </td>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_edit_materiel&amp;materiel_id=<?php echo $this->_tpl_vars['curr_stock']->_ref_materiel->materiel_id; ?>
" title="Modifier le matériel">
              <?php echo $this->_tpl_vars['curr_stock']->_ref_materiel->nom; ?>

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
  </tr>
</table>    