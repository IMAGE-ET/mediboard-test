{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{assign var=line_med value=$produits_cis.$code_cis}}
{{assign var=produit value=$line_med->_ref_produit}}

<tr>
  <th colspan="10" class="element">{{$produit->libelle_abrege}} {{$produit->dosage}}</th>
</tr>

{{assign var=infinite value=$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$dPconfig.dPstock.CProductStockService.infinite_quantity}}

{{assign var=quantite_administration value=$quantites.quantite_administration}}
{{assign var=quantite_dispensation value=$quantites.quantite_dispensation}}
{{if !$mode_nominatif}}
  {{assign var=patients value=$besoin_patient.$code_cis}}
{{/if}}
    
<tr>
  <!-- Quantite à administrer -->
  <td style="text-align: center;" class="text">
    <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-poso-{{$code_cis}}')">
      {{$quantite_administration}} 
		  {{if $line_med->_ref_produit_prescription->_id}}
		    {{$line_med->_ref_produit_prescription->unite_prise}}
		  {{else}}
		    {{$produit->_unite_administration}}
		  {{/if}}
    </span>
    <table id="tooltip-content-poso-{{$code_cis}}" style="display: none;" class="tbl">
    {{if $mode_nominatif}}
      {{if $_lines.$code_cis->_class_name == "CPrescriptionLineMedicament"}}
        <tr>
          <th>{{$_lines.$code_cis->_duree_prise}}</th>
        </tr>
        {{foreach from=$_lines.$code_cis->_ref_prises item=prise}}
          <tr>
            <td>{{$prise}}</td>
          </tr>
        {{/foreach}}
      {{else}}
        <tr>
          <td>{{$_lines.$code_cis->_posologie}} {{$_lines.$code_cis->_ref_perfusion->_frequence}}</td>
        </tr>
      {{/if}}
    {{else}}
      {{foreach from=$patients item=_patient}}
				{{if $_patient.quantite_administration}}
        {{assign var=patient value=$_patient.patient}}
        <tr>
          <th>{{$patient->_view}}</th>
          <td>
            {{$_patient.quantite_administration}} {{$produit->_unite_administration}} 
            {{if $produit->_unite_dispensation != $produit->_unite_administration}}
              ({{$_patient.quantite_dispensation}} {{$produit->_unite_dispensation}})
            {{/if}}
          </td>
        </tr>
				{{/if}}
      {{/foreach}}
    {{/if}}
    </table>
  </td>
  
  <!-- Quantite à dispenser -->
  <td style="text-align: center;">
    {{if array_key_exists($code_cis, $correction_dispensation)}}
      <!-- 
      <ul>
      {{foreach from=$correction_dispensation.$code_cis key=code_cip item=_corrections}}
        {{if $code_cip != "nb"}}
          <li>{{$_corrections.dispensation}}</li>
        {{/if}}
      {{/foreach}}
      </ul> -->
      -
    {{else}}
      {{$quantite_dispensation}}
    {{/if}}
  </td>
  
  <!-- Stock Pharmacie -->
  {{if !$infinite}}
   <!-- Affichage des stocks du service -->
   <td style="text-align: center;">
     {{if array_key_exists($code_cis, $stocks_pharmacie) && $stocks_pharmacie.$code_cis.total}}
       <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-stock-pharma{{$code_cis}}')">
         {{$stocks_pharmacie.$code_cis.total}}
       </span>
       <div id="tooltip-content-stock-pharma{{$code_cis}}" style="display: none">
	       <ul>
		       {{foreach from=$stocks_pharmacie.$code_cis key=_cip item=stock_pharma}}
		         {{if $_cip != "total"}}
			         {{assign var=_stock_produit value=$produits_cip.$_cip}}
			         {{assign var=deliv value=$delivrances.$code_cis.$_cip}}
			         {{if $stock_pharma}}
			           <li><strong>{{$stock_pharma}} {{$deliv->_ref_stock->_ref_product->_unit_title}}</strong> {{$_stock_produit.LIBELLE_PRODUIT}}</li>
			         {{/if}}
		         {{/if}}
		       {{/foreach}}
	       </ul>
       </div>
     {{else}}
       Aucun stock
     {{/if}} 
   </td>
  {{/if}}
 
 <td style="text-align: center;">
   {{if $line_med->_ref_produit_prescription->_id}}
	   {{$line_med->_ref_produit_prescription->unite_dispensation}}
	 {{else}}
     {{if array_key_exists($code_cis,$delivrances)}}
       {{foreach from=$delivrances.$code_cis key=code_cip item=delivrance}}
         {{if $delivrance->_ref_stock->_ref_product->packaging}}
     	     <label title="{{$delivrance->_ref_stock->_ref_product->_unit_title}}">
     	     {{mb_ditto name="unite_disp" value=$delivrance->_ref_stock->_ref_product->_unit_title|truncate:30}}<br />
     	     </label>
     	   {{/if}}
       {{/foreach}}
     {{/if}}
	 {{/if}}
 </td>
 
 <!-- Formulaire de dispensation -->
 <td style="text-align: left;">
 {{if $infinite}}
  {{if array_key_exists($code_cis,$delivrances)}}
    {{foreach from=$delivrances.$code_cis key=code_cip item=delivrance}}
      {{assign var=_produit_cip value=$produits_cip.$code_cip}}
      <form name="form-dispensation-{{$code_cis}}-{{$code_cip}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function(){ refreshLists('{{$code_cis}}'); } })">
       <input type="hidden" name="m" value="dPstock" />
       <input type="hidden" name="tab" value="{{$tab}}" />
       <input type="hidden" name="dosql" value="do_delivery_aed" />
       <input type="hidden" name="del" value="0" />
       <input type="hidden" name="date_dispensation" value="now" />
       <input type="hidden" name="stock_id" value="{{$delivrance->stock_id}}" />
       <input type="hidden" name="service_id" value="{{$delivrance->service_id}}" />
       {{if $mode_nominatif}}
       <input type="hidden" name="patient_id" value="{{$prescription->_ref_object->_ref_patient->_id}}" />
       {{/if}}
       {{if $delivrance->quantity == 0}}
         {{assign var=style value="opacity: 0.5; -moz-opacity: 0.5;"}}
           <script type="text/javascript">
             getForm("form-dispensation-{{$code_cis}}-{{$code_cip}}").up("tbody").hide().addClassName("done");
           </script>
       {{else}}
         {{assign var=style value=""}}
       {{/if}}
       
       {{$_produit_cip.LIBELLE_PRODUIT}}<br />
       <button type="submit" class="tick notext" title="Dispenser" style="{{$style}}">Dispenser</button>
       
       {{assign var=qty value=$delivrance->_ref_stock->_ref_product->_unit_quantity-0}}
       {{if $delivrance->_ref_stock->_ref_product->packaging && $qty}}
         {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cis-$code_cip" increment=1 size=3 min=0 style=$style 
           onchange="this.form._quantity_package.value = this.value/$qty"}}
         
         (soit <input type="text" name="_quantity_package" value="{{if $qty}}{{$delivrance->quantity/$qty}}{{else}}0{{/if}}" size="3" 
                onchange="$V(this.form.quantity, Math.round($V(this)*{{$qty}}))" style="{{$style}}" />
         {{$delivrance->_ref_stock->_ref_product->packaging}})
         <script type="text/javascript">
          getForm("form-dispensation-{{$code_cis}}-{{$code_cip}}")._quantity_package.addSpinner({min:0});
         </script>
       {{else}}
         {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cis-$code_cip" increment=1 size=3 min=0 style=$style}}
       {{/if}}
      </form>
      <br />
    {{/foreach}}
   {{else}}
     Aucun stock à la pharmacie 
     <button type="button" onclick="window.location.href='?m=dPstock&amp;tab=vw_idx_stock_group'" class="new">
       Créer
     </button>
   {{/if}}
 {{else}}
   {{if array_key_exists($code_cis,$delivrances)}}
     {{foreach from=$delivrances.$code_cis key=code_cip item=delivrance}}
     {{assign var=_produit_cip value=$produits_cip.$code_cip}}
     
       {{if $delivrance->_ref_stock->quantity>0}}
       <form name="form-dispensation-{{$code_cis}}-{{$code_cip}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshLists('{{$code_cis}}'); } })">
         <input type="hidden" name="m" value="dPstock" />
         <input type="hidden" name="tab" value="{{$tab}}" />
         <input type="hidden" name="dosql" value="do_delivery_aed" />
         <input type="hidden" name="del" value="0" />
         <input type="hidden" name="date_dispensation" value="now" />
         <input type="hidden" name="stock_id" value="{{$delivrance->stock_id}}" />
         <input type="hidden" name="service_id" value="{{$delivrance->service_id}}" />
         {{if $mode_nominatif}}
         <input type="hidden" name="patient_id" value="{{$prescription->_ref_object->_ref_patient->_id}}" />
         {{/if}}
         {{if $delivrance->quantity == 0}}
           {{assign var=style value="opacity: 0.5; -moz-opacity: 0.5;"}}
           <script type="text/javascript">
             getForm("form-dispensation-{{$code_cis}}-{{$code_cip}}").up("tbody").hide().addClassName("done");
           </script>
         {{else}}
           {{assign var=style value=""}}
         {{/if}}
         
         {{$_produit_cip.LIBELLE_PRODUIT}}<br />
         <button type="submit" class="tick notext" title="Dispenser" style="{{$style}}">Dispenser</button>
         
         {{assign var=qty value=$delivrance->_ref_stock->_ref_product->_unit_quantity-0}}
         {{if $delivrance->_ref_stock->_ref_product->packaging && $qty}}
           {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cis-$code_cip" increment=1 size=3 min=0 style=$style 
             onchange="this.form._quantity_package.value = this.value/$qty"}}
            
           (soit <input type="text" name="_quantity_package" value="{{if $qty}}{{$delivrance->quantity/$qty}}{{else}}0{{/if}}" size="3" 
                  onchange="$V(this.form.quantity, Math.round($V(this)*{{$qty}}))" style="{{$style}}" />
           {{$delivrance->_ref_stock->_ref_product->packaging}})
           <script type="text/javascript">
            getForm("form-dispensation-{{$code_cis}}-{{$code_cip}}")._quantity_package.addSpinner({min:0});
           </script>
         {{else}}
           {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cis-$code_cip" increment=1 size=3 min=0 style=$style}}
         {{/if}}
       </form>
       {{else}}
       {{$_produit_cip.LIBELLE_PRODUIT}}: Stock épuisé à la pharmacie
       {{/if}}
       
       <br />
     {{/foreach}}
   {{else}}
     Aucun stock à la pharmacie 
     <button type="button" onclick="window.location.href='?m=dPstock&amp;tab=vw_idx_stock_group'" class="new">
       Créer
     </button>
   {{/if}}
 {{/if}}
 </td>
 
 <!-- Affichage des dispensations deja effectuées -->
 <td style="text-align: left" class="text">  
   {{if array_key_exists($code_cis, $done)}}     
   {{foreach from=$done.$code_cis key=done_key item=done_by_cip name="done"}}
   	 {{if $done_key != "total" }}
     {{foreach from=$done_by_cip item=curr_done}}
       {{assign var=_produit_cip value=$produits_cip.$done_key}}
   	   {{foreach from=$curr_done->_ref_delivery_traces item=trace}}
           <div id="tooltip-content-{{$curr_done->_id}}" style="display: none;">
             {{$trace->quantity}} {{$trace->_ref_delivery->_ref_stock->_ref_product->_unit_title}} délivré le {{$trace->date_delivery|@date_format:"%d/%m/%Y"}} [{{$_produit_cip.LIBELLE_PRODUIT}}]
           </div>
           <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_done->_id}}')" style="white-space: nowrap;">
             <img src="images/icons/tick.png" title="Délivré" />
             {{$curr_done->quantity}} {{$curr_done->_ref_stock->_ref_product->_unit_title}} le {{$curr_done->date_dispensation|@date_format:"%d/%m/%Y"}} [{{$_produit_cip.LIBELLE_PRODUIT}}]
             <br />
           </span>
           {{foreachelse}}
             <form name="form-dispensation-del-{{$curr_done->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() { refreshLists('{{$code_cis}}'); } })">
               <input type="hidden" name="m" value="dPstock" />
               <input type="hidden" name="dosql" value="do_delivery_aed" />
               <input type="hidden" name="del" value="1" />
               <input type="hidden" name="delivery_id" value="{{$curr_done->_id}}" />
               <button type="submit" class="cancel notext" title="{{tr}}Cancel{{/tr}}">{{tr}}Cancel{{/tr}}</button>
             </form>
             {{$curr_done->quantity}}  {{$curr_done->_ref_stock->_ref_product->_unit_title}} le {{$curr_done->date_dispensation|@date_format:"%d/%m/%Y"}} [{{$_produit_cip.LIBELLE_PRODUIT}}]
             <br />
         {{/foreach}}
     {{foreachelse}}
     {{/foreach}}
     {{/if}}
   {{/foreach}}
   {{/if}}
  {{if !$mode_nominatif}}
    {{if array_key_exists($code_cis, $done_nominatif)}}
     <!-- Affichage des dispensations nominatives effectuées -->
      {{foreach from=$done_nominatif.$code_cis key=done_nom_key item=done_nom_by_cip}}
      	{{if $done_nom_key != "total"}}
      	{{foreach from=$done_nom_by_cip item=curr_done_nom name="done_nominatif"}}
      	   {{assign var=_produit_cip value=$produits_cip.$done_nom_key}}
           {{foreach from=$curr_done_nom->_ref_delivery_traces item=trace}}
             <div id="tooltip-content-{{$curr_done_nom->_id}}" style="display: none;">
               {{$trace->quantity}} {{$trace->_ref_delivery->_ref_stock->_ref_product->_unit_title}} délivré le {{$trace->date_delivery|@date_format:"%d/%m/%Y"}} à {{$trace->_ref_delivery->_ref_patient->_view}} [{{$_produit_cip.LIBELLE_PRODUIT}}]
             </div>
             <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_done_nom->_id}}')" style="white-space: nowrap;">
               <img src="images/icons/tick.png" title="Délivré" />
               {{$curr_done_nom->quantity}} {{$curr_done_nom->_ref_stock->_ref_product->_unit_title}} le {{$curr_done_nom->date_dispensation|@date_format:"%d/%m/%Y"}} à {{$trace->_ref_delivery->_ref_patient->_view}} [{{$_produit_cip.LIBELLE_PRODUIT}}]
               <br />
             </span>
           {{foreachelse}}
             <span style="white-space: nowrap;">
               {{$curr_done_nom->quantity}} {{$curr_done_nom->_ref_stock->_ref_product->_unit_title}} le {{$curr_done_nom->date_dispensation|@date_format:"%d/%m/%Y"}} à {{$curr_done_nom->_ref_patient->_view}}
               [{{$_produit_cip.LIBELLE_PRODUIT}}]
               <br />
             </span>
           {{/foreach}}
       {{/foreach}}
       {{/if}}
     {{/foreach}}
     {{/if}}
   {{/if}}
   
   {{if $mode_nominatif}}
      <!-- Affichage des dispensations globales effectuées -->
     {{if array_key_exists($code_cis, $done_global)}}
      {{foreach from=$done_global.$code_cis key=done_glob_key item=done_glob_by_cip name="done_global"}}
        {{if $done_glob_key != "total"}}
          {{foreach from=$done_glob_by_cip item=curr_done_glob}}
           {{assign var=_produit_cip value=$produits_cip.$done_glob_key}}
           {{foreach from=$curr_done_glob->_ref_delivery_traces item=trace}}
             <div id="tooltip-content-{{$curr_done_glob->_id}}" style="display: none;">
                {{$trace->quantity}} {{$trace->_ref_delivery->_ref_stock->_ref_product->_unit_title}} délivré le {{$trace->date_delivery|@date_format:"%d/%m/%Y"}} [{{$_produit_cip.LIBELLE_PRODUIT}}]
             </div>
             <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_done_glob->_id}}')">
               <img src="images/icons/tick.png" title="Délivré" />
               {{$curr_done_glob->quantity}} {{$curr_done_glob->_ref_stock->_ref_product->_unit_title}} le {{$curr_done_glob->date_dispensation|@date_format:"%d/%m/%Y"}} [{{$_produit_cip.LIBELLE_PRODUIT}}]
               <br />
             </span>
           {{foreachelse}}
             {{$curr_done_glob->quantity}} {{$curr_done_glob->_ref_stock->_ref_product->_unit_title}} le {{$curr_done_glob->date_dispensation|@date_format:"%d/%m/%Y"}} (Global) [{{$_produit_cip.LIBELLE_PRODUIT}}]
             <br />
           {{/foreach}}
         {{/foreach}}
       {{/if}}
     {{/foreach}}
     {{/if}}
   {{/if}}
 </td>
 
 {{if !$infinite_service}}
 <!-- Affichage des stocks du service -->
 <td style="text-align: center;">
   {{if array_key_exists($code_cis, $stocks_service) && $stocks_service.$code_cis.total}}
   	 <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-stock-service{{$code_cis}}')">
   		  {{$stocks_service.$code_cis.total}}
   	 </span>
   	 <div id="tooltip-content-stock-service{{$code_cis}}" style="display: none;">       
       <ul>
	       {{foreach from=$stocks_service.$code_cis key=_cip item=stock}}
	         {{if $_cip != "total"}}
		         {{assign var=_stock_produit value=$produits_cip.$_cip}}
		         {{if $stock->quantity}}
		           <li><strong>{{$stock->quantity}} </strong>{{$_stock_produit.LIBELLE_PRODUIT}}</li>
		         {{/if}}
	         {{/if}}
	       {{/foreach}}
       </ul>
     </div>
   {{else}}
     Aucun stock
   {{/if}} 
 </td>
 {{/if}}
   
</tr>