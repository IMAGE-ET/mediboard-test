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

{{assign var=infinite value=$conf.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$conf.dPstock.CProductStockService.infinite_quantity}}

{{assign var=quantite_administration value=$quantites.quantite_administration}}
{{assign var=quantite_dispensation value=$quantites.quantite_dispensation}}

{{if !$mode_nominatif}}
  {{assign var=patients value=$besoin_patient.$code_cis}}
{{/if}}
    
<td class="text">
	<strong>
	  {{$produit->libelle_abrege}} {{$produit->dosage}}
	</strong>
</td>
{{if $mode_nominatif}}
	<td class="text">
		{{foreach from=$_lines.$code_cis item=_lines_by_guid}}
		  <span href="#1" onmouseover="ObjectTooltip.createEx(this, '{{$_lines_by_guid->_guid}}')">
		    {{if $_lines_by_guid instanceof CPrescriptionLineMedicament}}
		      {{foreach from=$_lines_by_guid->_ref_prises item=prise name="prises"}}
		        {{$prise}}
		        {{if !$smarty.foreach.prises.last}}, {{/if}}
		      {{/foreach}}
		    {{else}}
		      {{$_lines_by_guid->_posologie}} {{$_lines_by_guid->_ref_prescription_line_mix->_frequence}}
		    {{/if}}
		  </span>
		  <br />
		{{/foreach}}
	</td>
{{/if}}
<!-- Quantite à administrer -->
<td colspan="2">
  <span {{if !$mode_nominatif}}onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-poso-{{$code_cis}}')"{{/if}}>
    {{$quantite_administration}} 
	  {{$produit->_unite_administration}}
  </span>
	
	{{if !$mode_nominatif}}
    <table id="tooltip-content-poso-{{$code_cis}}" style="display: none;" class="tbl">
      <tr>
         <th>Patient</th>
				 <th>Quantité</th>
			</tr>
			{{foreach from=$patients item=_patient}}
				{{if $_patient.quantite_administration}}
        {{assign var=patient value=$_patient.patient}}
        <tr>
          <td>{{$patient->_view}}</td>
          <td>
            {{$_patient.quantite_administration}} {{$produit->_unite_administration}} 
            {{if $produit->_unite_dispensation != $produit->_unite_administration}}
              ({{$_patient.quantite_dispensation}} {{$produit->_unite_dispensation}})
            {{/if}}
          </td>
        </tr>
				{{/if}}
      {{/foreach}}
    </table>
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
   {{/if}} 
 </td>
{{/if}}

<!-- Formulaire de dispensation -->
<td style="text-align: left;" class="text">
{{if array_key_exists($code_cis,$delivrances)}}
  {{foreach from=$delivrances.$code_cis key=code_cip item=delivrance}}
    {{assign var=_produit_cip value=$produits_cip.$code_cip}}
		{{assign var=qty value=$delivrance->_ref_stock->_ref_product->_unit_quantity-0}}
		{{if $infinite || ($delivrance->_ref_stock->quantity>0 && !$infinite)}}
		
     <form name="form-dispensation-{{$code_cis}}-{{$code_cip}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function(){ refreshLists('{{$code_cis}}'); } })">
       <input type="hidden" name="m" value="dPstock" />
       <input type="hidden" name="tab" value="{{$tab}}" />
       <input type="hidden" name="dosql" value="do_delivery_aed" />
       <input type="hidden" name="del" value="0" />
       <input type="hidden" name="date_dispensation" value="now" />
       <input type="hidden" name="datetime_min" value="{{$datetime_min}}" />
       <input type="hidden" name="datetime_max" value="{{$datetime_max}}" />
			 
			 {{if $mode_nominatif}}
			 {{assign var=_prises value=$prises.$code_cis.$code_cip}}
			 <input type="hidden" name="_prises" value='{{$_prises|@json|addslashes}}' /> 
			 {{/if}}
			 
	     <input type="hidden" name="stock_id" value="{{$delivrance->stock_id}}" />
	     <input type="hidden" name="stock_class" value="{{$delivrance->stock_class}}" />
	     <input type="hidden" name="service_id" value="{{$delivrance->service_id}}" />
       {{if $mode_nominatif}}
         <input type="hidden" name="patient_id" value="{{$prescription->_ref_object->_ref_patient->_id}}" />
       {{/if}}
       
       {{if $delivrance->quantity == 0}}
         {{assign var=style value="opacity: 0.5;"}}
         <script type="text/javascript">
         	 toggleLineDispensation("form-dispensation-{{$code_cis}}-{{$code_cip}}", true);
         </script>
       {{else}}
			   <script type="text/javascript">
			   	 toggleLineDispensation("form-dispensation-{{$code_cis}}-{{$code_cip}}", false);
         </script>
         {{assign var=style value=""}}
       {{/if}}
       
       <strong>{{$_produit_cip.LIBELLE_PRODUIT}}</strong><br />
			 
			 <div class="opacity-50" style="float: right;">
           (soit <input type="text" name="_quantity_package" value="{{if $qty}}{{$delivrance->quantity/$qty}}{{else}}0{{/if}}" size="3" 
                  onchange="$V(this.form.quantity, Math.round($V(this)*{{$qty}}))" style="{{$style}}" />
           {{$delivrance->_ref_stock->_ref_product->packaging}})
         </div>
       <button type="submit" class="tick notext" title="Dispenser" style="{{$style}}">Dispenser</button>

       {{if $delivrance->_ref_stock->_ref_product->packaging && $qty}}
				 {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cis-$code_cip" increment=1 size=3 min=0 style=$style onchange="this.form._quantity_package.value = (this.value/$qty).toFixed(2)"}}
				 {{$delivrance->_ref_stock->_ref_product->_unit_title|truncate:30}}
         <script type="text/javascript">
          getForm("form-dispensation-{{$code_cis}}-{{$code_cip}}")._quantity_package.addSpinner({min:0});
         </script>
       {{else}}
         {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cis-$code_cip" increment=1 size=3 min=0 style=$style}}
       {{/if}}
			 
      </form>
		{{elseif !$infinite}}
       {{$_produit_cip.LIBELLE_PRODUIT}}: Stock épuisé à la pharmacie
     {{/if}}
    <br />
  {{/foreach}}
 {{else}} 
	<div class="opacity-50 empty">
		<a class="button new" type="button" href="?m=dPstock&amp;tab=vw_idx_stock_group" style="float: right;">
      Créer
    </a>
	 	Aucun stock à la pharmacie 
	</div>
 {{/if}}
</td>


<!-- Affichage des dispensations deja effectuées -->
<td class="text" style="text-align: center;">  

 {{if $nb_done_total.$code_cis}}     
 <span onmouseover="ObjectTooltip.createDOM(this, 'dispensations-{{$code_cis}}');">
   {{$nb_done_total.$code_cis}} préparations
 </span>
 {{/if}}
 
 <div id="dispensations-{{$code_cis}}" style="display: none;">
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


 {{if array_key_exists($code_cis, $done_delivery)}}
  {{foreach from=$done_delivery.$code_cis key=done_glob_key item=done_glob_by_cip}}
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
         {{$curr_done_glob->quantity}} {{$curr_done_glob->_ref_stock->_ref_product->_unit_title}} le {{$curr_done_glob->date_dispensation|@date_format:"%d/%m/%Y"}} [{{$_produit_cip.LIBELLE_PRODUIT}}]
         <br />
       {{/foreach}}
     {{/foreach}}
   {{/if}}
 {{/foreach}}
 {{/if}}
 </div>
</td>

{{if !$infinite_service}}
 <!-- Affichage des stocks du service -->
 {{if array_key_exists($code_cis, $stocks_service) && $stocks_service.$code_cis.total}}
	 <td style="text-align: center;">
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
   </td>
 {{else}}
   <td class="empty">Aucun stock</td>
 {{/if}} 
{{/if}}
 