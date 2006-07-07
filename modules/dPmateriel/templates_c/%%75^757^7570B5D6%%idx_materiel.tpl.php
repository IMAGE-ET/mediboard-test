<?php /* Smarty version 2.6.13, created on 2006-07-05 17:11:48
         compiled from idx_materiel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'nl2br', 'idx_materiel.tpl', 23, false),)), $this); ?>
<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Nom</th>
          <th>Description</th>
          <th>Code Barre</th>
        </tr>
        <?php $_from = $this->_tpl_vars['listMateriel']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_materiel']):
?>
        <tr>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_edit_materiel&amp;materiel_id=<?php echo $this->_tpl_vars['curr_materiel']->materiel_id; ?>
" title="Modifier le matériel">
              <?php echo $this->_tpl_vars['curr_materiel']->materiel_id; ?>

            </a>
          </td>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_edit_materiel&amp;materiel_id=<?php echo $this->_tpl_vars['curr_materiel']->materiel_id; ?>
" title="Modifier le matériel">
              <?php echo $this->_tpl_vars['curr_materiel']->nom; ?>

            </a>
          </td>
          <td><?php echo ((is_array($_tmp=$this->_tpl_vars['curr_materiel']->description)) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</td>
          <td><?php echo $this->_tpl_vars['curr_materiel']->code_barre; ?>
</td>
        </tr>
        <?php endforeach; endif; unset($_from); ?>
        
      </table>
    </td>
  </tr>
</table>