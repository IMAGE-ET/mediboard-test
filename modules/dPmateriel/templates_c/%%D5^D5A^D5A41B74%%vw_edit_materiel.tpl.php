<?php /* Smarty version 2.6.13, created on 2006-07-05 15:53:36
         compiled from vw_edit_materiel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'vw_edit_materiel.tpl', 35, false),)), $this); ?>
<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button" href="index.php?m=dPmateriel&amp;tab=vw_edit_materiel&amp;materiel_id=0">
        Créer une nouvelle fiche
      </a>
      <form name="editMat" action="./index.php?m=<?php echo $this->_tpl_vars['m']; ?>
" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_materiel_aed" />
	  <input type="hidden" name="materiel_id" value="<?php echo $this->_tpl_vars['materiel']->materiel_id; ?>
" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <?php if ($this->_tpl_vars['materiel']->materiel_id): ?>
          <th class="title" colspan="2" style="color:#f00;">Modification de la fiche <?php echo $this->_tpl_vars['materiel']->_view; ?>
</th>
          <?php else: ?>
          <th class="title" colspan="2">Création d'une fiche</th>
          <?php endif; ?>
        </tr>   
        <tr>
          <th><label for="nom" title="Nom du matériel, obligatoire">Nom</label></th>
          <td><input name="nom" title="<?php echo $this->_tpl_vars['materiel']->_props['nom']; ?>
" type="text" value="<?php echo $this->_tpl_vars['materiel']->nom; ?>
" /></td>
        </tr>
        <tr>
          <th><label for="nom" title="Code Barre du matériel, numérique">Code Barre</label></th>
          <td><input name="code_barre" title="<?php echo $this->_tpl_vars['materiel']->_props['code_barre']; ?>
" type="text" value="<?php echo $this->_tpl_vars['materiel']->code_barre; ?>
" /></td>
        </tr>
        <tr>
          <th><label for="nom" title="Description du matériel">Description</label></th>
          <td><textarea title="<?php echo $this->_tpl_vars['materiel']->_props['description']; ?>
" name="description"><?php echo $this->_tpl_vars['materiel']->description; ?>
</textarea></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="submit">Valider</button>
            <?php if ($this->_tpl_vars['materiel']->materiel_id): ?>
              <button type="button" onclick="confirmDeletion(this.form,{typeName:'le matériel',objName:'<?php echo ((is_array($_tmp=$this->_tpl_vars['materiel']->_view)) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
'})">Supprimer</button>
            <?php endif; ?>
          </td>
        </tr>        
      </table>
      </form>
    </td>
    <td class="halfPane">
       <?php if ($this->_tpl_vars['materiel']->_ref_stock): ?>
         <table class="tbl">
         <tr>
           <th>Groupe</th>
           <th>Seuil de Commande</th>
           <th>Quantité</th>
         </tr>
         <?php $_from = $this->_tpl_vars['materiel']->_ref_stock; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_stock']):
?>
         <tr>
           <td><?php echo $this->_tpl_vars['curr_stock']->_ref_group->text; ?>
</td>
           <td><?php echo $this->_tpl_vars['curr_stock']->seuil_cmd; ?>
</td>
           <td><?php echo $this->_tpl_vars['curr_stock']->quantite; ?>
</td>
         </tr>
         <?php endforeach; endif; unset($_from); ?>
       </table>
       <?php endif; ?>
    </td>    
  </tr>
</table>