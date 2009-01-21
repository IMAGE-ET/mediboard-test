{{if $now < $date_min || $now > $date_max}}
	<div class="small-info">
	  La date courante n'est pas comprise dans l'intervalle spécifié, les dispensations effectuées ne seront pas affichées.
	</div>
{{/if}}

<script type="text/javascript">
  $$('a[href=#list-dispensations] small').first().update('({{$dispensations|@count}})');
  
  loadSuivi = function(sejour_id) {
   var urlSuivi = new Url;
   urlSuivi.setModuleAction("dPhospi", "httpreq_vw_dossier_suivi");
   urlSuivi.addParam("sejour_id", sejour_id);
   urlSuivi.requestUpdate("list-transmissions", { waitingText: null } );
  }
 
	submitSuivi = function(oForm) {
	  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { 
	    loadSuivi(oForm.sejour_id.value);
	  } });
	}

</script>

{{if $dPconfig.dPstock.CProductStockGroup.infinite_quantity == 1}}
  {{assign var=infinite value=1}}
{{else}}
  {{assign var=infinite value=0}}
{{/if}}

<table class="tbl">
  {{if $mode_nominatif}}
  <tr>
    <th colspan="10" class="title">
      Dispensation pour {{$prescription->_ref_object->_ref_patient->_view}}
      <button type="button" onclick="dispenseAll()" class="tick">Tout dispenser</button>
    </th>
  </tr>
  {{/if}}
  <tr>
    <th>Quantité à administrer</th>
    <th>Quantité à dispenser</th>
    {{if !$infinite}}
      <th>Stock pharmacie</th>
    {{/if}}
    <th>Déjà effectuées</th>
    <th style="width: 30%">Dispensation</th>
    <th>Stock du service</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
  </tr>
  {{foreach from=$dispensations key=code_cip item=quantites}}
    {{assign var=quantite_administration value=$quantites.quantite_administration}}
    {{assign var=quantite_dispensation value=$quantites.quantite_dispensation}}
    {{if !$mode_nominatif}}
      {{assign var=patients value=$besoin_patient.$code_cip}}
    {{/if}}
    {{assign var=produit value=$produits.$code_cip}}
    <tr>
      <th colspan="10" class="element">{{$produit->libelle}}</th>
    </tr>
    <tr>
      <!-- Quantite à administrer -->
      <td>
        <div onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$code_cip}}'} })" class="tooltip-trigger">
          <a href="#1">{{$quantite_administration}} {{$produit->_unite_administration}}</a>
        </div>
        <table id="tooltip-content-{{$code_cip}}" style="display: none;" class="tbl">
        {{if $mode_nominatif}}
          <tr>
            <th>{{$lines.$code_cip->_duree_prise}}</th>
          </tr>
          {{foreach from=$lines.$code_cip->_ref_prises item=prise}}
            <tr>
              <td>{{$prise}}</td>
            </tr>
          {{/foreach}}
        {{else}}
          {{foreach from=$patients item=_patient}}
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
          {{/foreach}}
        {{/if}}
        </table>
      </td>
      <!-- Quantite à dispenser pour permettre l'administration -->
      <td>{{$quantite_dispensation}}</td>
      {{if !$infinite}}
        <td>
         {{if array_key_exists($code_cip,$delivrances)}}
           {{assign var=delivrance value=$delivrances.$code_cip}}
           {{$delivrance->_ref_stock->quantity}}
         {{else}}
           0
         {{/if}}
        </td>
      {{/if}}
     <td style="text-align: left">       
       {{foreach from=$done.$code_cip item=curr_done name="done"}}
         {{if !$smarty.foreach.done.first}}
           {{foreach from=$curr_done->_ref_delivery_traces item=trace}}
             <div id="tooltip-content-{{$curr_done->_id}}" style="display: none;">{{$trace->quantity}} délivré le {{$trace->date_delivery|@date_format:"%d/%m/%Y"}}</div>
             <div class="tooltip-trigger" 
                  onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$curr_done->_id}}'} })">
               {{$curr_done->quantity}} le {{$curr_done->date_dispensation|@date_format:"%d/%m/%Y"}}
               <img src="images/icons/tick.png" alt="Délivré" title="Délivré" />
             </div>
             {{foreachelse}}
               <form name="form-dispensation-del-{{$curr_done->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
                 <input type="hidden" name="m" value="dPstock" />
                 <input type="hidden" name="dosql" value="do_delivery_aed" />
                 <input type="hidden" name="del" value="1" />
                 <input type="hidden" name="delivery_id" value="{{$curr_done->_id}}" />
                 <button type="submit" class="cancel notext" title="{{tr}}Cancel{{/tr}}">{{tr}}Cancel{{/tr}}</button>
               </form>
               {{$curr_done->quantity}} le {{$curr_done->date_dispensation|@date_format:"%d/%m/%Y"}}
               <br />
           {{/foreach}}
         {{/if}}
       {{foreachelse}}
         Aucune
       {{/foreach}}
     </td>
     <td style="text-align: left; width: 0.1%;">
     {{if $infinite}}
      {{if array_key_exists($code_cip,$delivrances)}}
        {{assign var=delivrance value=$delivrances.$code_cip}}
        <script type="text/javascript">prepareForm('form-dispensation-{{$code_cip}}');</script>
        <form name="form-dispensation-{{$code_cip}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
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
         {{else}}
           {{assign var=style value=""}}
         {{/if}}
         
         <button type="submit" class="tick notext" title="Dispenser" style="{{$style}} float: left;">Dispenser</button>
         
         {{assign var=qty value=$delivrance->_ref_stock->_ref_product->_unit_quantity-0}}
         {{if $delivrance->_ref_stock->_ref_product->packaging && $qty}}
           {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cip" increment=1 size=3 min=0 style=$style 
             onchange="this.form._quantity_package.value = this.value/$qty"}}
           
           (soit <input type="text" name="_quantity_package" value="{{if $qty}}{{$delivrance->quantity/$qty}}{{else}}0{{/if}}" size="3" 
                  onchange="$V(this.form.quantity, Math.round($V(this)*{{$qty}}))" style="{{$style}}" />
           {{$delivrance->_ref_stock->_ref_product->packaging}})
           <script type="text/javascript">
            getForm("form-dispensation-{{$code_cip}}")._quantity_package.addSpinner({min:0});
           </script>
         {{else}}
           {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cip" increment=1 size=3 min=0 style=$style}}
         {{/if}}
        </form>
       {{else}}
         Pas de stock à la pharmacie 
         <button type="button" onclick="window.location.href='?m=dPstock&amp;tab=vw_idx_stock_group'" class="new">
           Créer
         </button>
       {{/if}}
     {{else}}
       {{if array_key_exists($code_cip,$delivrances)}}
         {{assign var=delivrance value=$delivrances.$code_cip}}
         {{if $delivrance->_ref_stock->quantity>0}}
         <script type="text/javascript">prepareForm('form-dispensation-{{$code_cip}}');</script>
         <form name="form-dispensation-{{$code_cip}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
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
           {{else}}
             {{assign var=style value=""}}
           {{/if}}
           
           <button type="submit" class="tick notext" title="Dispenser" style="{{$style}} float: left;">Dispenser</button>
           
           {{assign var=qty value=$delivrance->_ref_stock->_ref_product->_unit_quantity-0}}
           {{if $delivrance->_ref_stock->_ref_product->packaging && $qty}}
             {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cip" increment=1 size=3 min=0 style=$style 
               onchange="this.form._quantity_package.value = this.value/$qty"}}
             
             (soit <input type="text" name="_quantity_package" value="{{if $qty}}{{$delivrance->quantity/$qty}}{{else}}0{{/if}}" size="3" 
                    onchange="$V(this.form.quantity, Math.round($V(this)*{{$qty}}))" style="{{$style}}" />
             {{$delivrance->_ref_stock->_ref_product->packaging}})
             <script type="text/javascript">
              getForm("form-dispensation-{{$code_cip}}")._quantity_package.addSpinner({min:0});
             </script>
           {{else}}
             {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cip" increment=1 size=3 min=0 style=$style}}
           {{/if}}
         </form>
         {{else}}
         Stock épuisé à la pharmacie
         {{/if}}
       {{else}}
         Pas de stock à la pharmacie 
         <button type="button" onclick="window.location.href='?m=dPstock&amp;tab=vw_idx_stock_group'" class="new">
           Créer
         </button>
       {{/if}}
     {{/if}}
     </td>
     <td class="text">
     {{if $stocks_service.$code_cip}}
       {{$stocks_service.$code_cip->quantity-0}}
     {{/if}}
     </td>
     <td>{{$produit->_unite_dispensation}}</td>   
   </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductDelivery.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>