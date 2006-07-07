<?php /* Smarty version 2.6.13, created on 2006-07-06 12:17:47
         compiled from vw_idx_category.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'vw_idx_category.tpl', 49, false),)), $this); ?>
<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button" href="index.php?m=dPmateriel&amp;tab=vw_idx_category&amp;category_id=0">
        Créer une nouvelle catégorie
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Catégorie</th>
        </tr>
        <?php $_from = $this->_tpl_vars['listCategory']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_category']):
?>
        <tr>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_category&amp;category_id=<?php echo $this->_tpl_vars['curr_category']->category_id; ?>
" title="Modifier la catégorie">
              <?php echo $this->_tpl_vars['curr_category']->category_id; ?>

            </a>
          </td>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_category&amp;category_id=<?php echo $this->_tpl_vars['curr_category']->category_id; ?>
" title="Modifier le catégorie">
              <?php echo $this->_tpl_vars['curr_category']->category_name; ?>

            </a>
          </td>
        </tr>
        <?php endforeach; endif; unset($_from); ?>        
      </table>  
    </td>
    <td class="halfPane">
      <form name="editCat" action="./index.php?m=<?php echo $this->_tpl_vars['m']; ?>
" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_category_aed" />
	  <input type="hidden" name="category_id" value="<?php echo $this->_tpl_vars['category']->category_id; ?>
" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <?php if ($this->_tpl_vars['category']->category_id): ?>
          <th class="title" colspan="2" style="color:#f00;">Modification de la catégorie <?php echo $this->_tpl_vars['category']->_view; ?>
</th>
          <?php else: ?>
          <th class="title" colspan="2">Création d'une fiche</th>
          <?php endif; ?>
        </tr> 
        <tr>
          <th><label for="category_name" title="Nom de la catégorie, obligatoire">Catégorie</label></th>
          <td><input name="category_name" title="<?php echo $this->_tpl_vars['category']->_props['category_name']; ?>
" type="text" value="<?php echo $this->_tpl_vars['category']->category_name; ?>
" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="submit">Valider</button>
            <?php if ($this->_tpl_vars['category']->category_id): ?>
              <button type="button" onclick="confirmDeletion(this.form,{typeName:'la catégorie',objName:'<?php echo ((is_array($_tmp=$this->_tpl_vars['category']->_view)) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
'})">Supprimer</button>
            <?php endif; ?>
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>
</table>