<?php /* Smarty version 2.6.18, created on 2008-03-07 15:49:42
         compiled from vw_idx_societe.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'mb_include_script', 'vw_idx_societe.tpl', 1, false),array('function', 'mb_label', 'vw_idx_societe.tpl', 56, false),array('function', 'mb_field', 'vw_idx_societe.tpl', 57, false),array('modifier', 'cleanField', 'vw_idx_societe.tpl', 23, false),array('modifier', 'nl2br', 'vw_idx_societe.tpl', 32, false),array('modifier', 'JSAttribute', 'vw_idx_societe.tpl', 97, false),array('modifier', 'string_format', 'vw_idx_societe.tpl', 122, false),)), $this); ?>
<?php echo smarty_function_mb_include_script(array('module' => 'dPpatients','script' => 'autocomplete'), $this);?>


<script type="text/javascript">
function pageMain() {
  initInseeFields("edit_societe", "postal_code", "city");
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="2">
      <a class="buttonnew" href="?m=dPstock&amp;tab=vw_idx_societe&amp;societe_id=0">
        Nouvelle société
      </a>
      <table class="tbl">
        <tr>
          <th>Société</th>
          <th>Correspondant</th>
          <th>Adresse</th>
          <th>Téléphone</th>
          <th>E-Mail</th>
        </tr>
        <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['list_societes']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_societe']):
?>
        <tr <?php if (smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_id) == smarty_modifier_cleanField($this->_tpl_vars['societe']->_id)): ?>class="selected"<?php endif; ?>>
          <td class="text">
            <a href="?m=dPstock&amp;tab=vw_idx_societe&amp;societe_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_id); ?>
" title="Modifier la société">
              <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->_view); ?>

            </a>
          </td>
          <td class="text"><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->contact_name); ?>
 <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->contact_surname); ?>
</td>
          <td class="text">
            <?php echo ((is_array($_tmp=smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->address))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
<br /><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->postal_code); ?>
 <?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->city); ?>

          </td>
          <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->phone); ?>
</td>
          <td><a href="mailto:<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->email); ?>
"><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_societe']->email); ?>
</a></td>
        </tr>
        <?php endforeach; endif; unset($_from); ?>       
        
      </table>
    </td>
    <td class="halfPane">
      <?php if (smarty_modifier_cleanField($this->_tpl_vars['can']->edit)): ?>
      <form name="edit_societe" action="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_societe_aed" />
	  <input type="hidden" name="societe_id" value="<?php echo smarty_modifier_cleanField($this->_tpl_vars['societe']->_id); ?>
" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <?php if (smarty_modifier_cleanField($this->_tpl_vars['societe']->_id)): ?>
          <th class="title modify" colspan="2">Modification du societe <?php echo smarty_modifier_cleanField($this->_tpl_vars['societe']->_view); ?>
</th>
          <?php else: ?>
          <th class="title" colspan="2">Création d'une Societé</th>
          <?php endif; ?>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'name'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'name'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'address'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'address'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'postal_code'), $this);?>
</th>
          <td>
      		<?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'postal_code','size' => '31','maxlength' => '5'), $this);?>

      		<div style="display:none;" class="autocomplete" id="postal_code_auto_complete"></div>
    	  </td>
        </tr>
        <tr> 
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'city'), $this);?>
</th>
          <td>
      		<?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'city','size' => '31'), $this);?>

      		<div style="display:none;" class="autocomplete" id="city_auto_complete"></div>
    	  </td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'phone'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'phone'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'email'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'email'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'contact_name'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'contact_name'), $this);?>
</td>
        </tr>
        <tr>
          <th><?php echo smarty_function_mb_label(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'contact_surname'), $this);?>
</th>
          <td><?php echo smarty_function_mb_field(array('object' => smarty_modifier_cleanField($this->_tpl_vars['societe']),'field' => 'contact_surname'), $this);?>
</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            <?php if (smarty_modifier_cleanField($this->_tpl_vars['societe']->_id)): ?>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la societé',objName:'<?php echo ((is_array($_tmp=$this->_tpl_vars['societe']->_view)) ? $this->_run_mod_handler('JSAttribute', true, $_tmp) : JSAttribute($_tmp)); ?>
'})">Supprimer</button>
            <?php endif; ?>
          </td>
        </tr> 
      </table>
      </form>
      <?php endif; ?>
  <?php if (smarty_modifier_cleanField($this->_tpl_vars['societe']->_id)): ?>
      <button class="new" type="button" onclick="window.location='?m=dPstock&amp;tab=vw_idx_reference&amp;reference_id=0&amp;societe_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['societe']->_id); ?>
'">
        Nouvelle référence
      </button>
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Fournit ces références</th>
        </tr>
        <tr>
           <th>Produit</th>
           <th>Quantité</th>
           <th>Prix</th>
           <th>Prix Unitaire</th>
         </tr>
         <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['societe']->_ref_product_references); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_reference']):
?>
         <tr>
           <td><a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_reference&amp;reference_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_id); ?>
" title="Voir ou modifier la référence"><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_reference']->_ref_product->_view); ?>
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
           <td class="button" colspan="4">Aucune référence trouvée</td>
         </tr>
         <?php endif; unset($_from); ?>
       </table>
      <button class="new" type="button" onclick="window.location='?m=dPproduct&amp;tab=vw_idx_product&amp;product_id=0&amp;societe_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['societe']->_id); ?>
'">
        Nouveau produit
      </button>
      <table class="tbl">
        <tr>
          <th class="title" colspan="3">Fabrique ces produits</th>
        </tr>
        <tr>
           <th>Nom</th>
           <th>Description</th>
           <th>Code barre</th>
         </tr>
         <?php $_from = smarty_modifier_cleanField($this->_tpl_vars['societe']->_ref_products); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr_product']):
?>
         <tr>
           <td><a href="?m=<?php echo smarty_modifier_cleanField($this->_tpl_vars['m']); ?>
&amp;tab=vw_idx_product&amp;product_id=<?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_id); ?>
" title="Voir ou modifier le produit"><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->_view); ?>
</a></td>
           <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->description); ?>
</td>
           <td><?php echo smarty_modifier_cleanField($this->_tpl_vars['curr_product']->barcode); ?>
</td>
         </tr>
         <?php endforeach; else: ?>
         <tr>
           <td class="button" colspan="3">Aucun produit trouvé</td>
         </tr>
         <?php endif; unset($_from); ?>
       </table>
    </td>
  </tr>
  <?php endif; ?>
</table>