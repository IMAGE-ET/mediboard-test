<script type="text/javascript">

function viewFullAlertes() {
  {{if $prescription->_id}}
  var url = new Url;
  url.setModuleAction("dPprescription", "vw_full_alertes");
  url.addParam("prescription_id", {{$prescription->_id}});
  url.popup(700, 550, "Alertes");
  {{/if}}
}

</script>


{{if !$prescription->_id && $mode_protocole}}
<form action="?m=dPprescription" method="post" name="addProtocolePresc" onsubmit="return checkForm(this);">	
   <input type="hidden" name="m" value="dPprescription" />
   <input type="hidden" name="dosql" value="do_prescription_aed" />
   <input type="hidden" name="prescription_id" value="" />
   <input type="hidden" name="del" value="0" />
   <input type="hidden" name="object_class" value=""/>
   <input type="hidden" name="object_id" value=""/>
   <input type="hidden" name="praticien_id" value="" />
   <input type="hidden" name="callback" value="Prescription.reloadProt" />
   
   <table class="form">
     <tr>
       <th class="category" colspan="2">
         Création d'un protocole
       </th>
    </tr>
    <tr>
      <th>  
        {{mb_label object=$protocole field="libelle"}}
      </td>
      <td>
		    {{mb_field object=$protocole field="libelle" class="notNull"}}  
      </td>
    </tr>
    <tr>
      <th>
			  {{mb_label object=$protocole field="object_class"}}
			</td>
			<td>
			  {{mb_field object=$protocole field="object_class"}}  
			</td>
	  </tr>
	  <tr>
	   <td colspan="2" style="text-align: center">
			  <button type="button" onclick="addProtocole();" class="new">Créer une protocole</button>
	   </td>  
	  </tr>
  </table>
</form>

{{/if}}
    
{{if $prescription->_id}}
<table class="form">
  <tr>
    <th class="title">
      {{if !$mode_protocole}}
      <button type="button" class="cancel" onclick="Prescription.close('{{$prescription->object_id}}','{{$prescription->object_class}}')" style="float: left">
        Fermer 
      </button>
      {{/if}}
      <button type="button" class="print notext" onclick="Prescription.print('{{$prescription->_id}}')" style="float: right">
        Print
      </button>
      {{if $mode_protocole}}
      <!-- Formulaire de modification du libelle de la prescription -->
      <form name="addLibelle-{{$prescription->_id}}" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
        <input type="text" name="libelle" value="{{$prescription->libelle}}" 
               onchange="submitFormAjax(this.form, 'systemMsg', { 
                 onComplete : function() { 
                   reloadProtocoles({{$prescription->praticien_id}}) 
                 } })" />
      </form>
      <button class="tick notext"></button>
      {{else}}
        {{$prescription->_view}}
      {{/if}}
    </th>
  </tr>
  {{if !$mode_protocole}}
  <tr>
    <td>
      Protocoles de {{$praticien->_view}}
      <!-- Formulaire de selection protocole -->
      <form name="applyProtocole" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
        <select name="protocole_id" onchange="submitFormAjax(this.form, 'systemMsg'); this.value='';">
          <option value="">&mdash; Sélection d'un protocole</option>
          {{foreach from=$protocoles item=protocole}}
          <option value="{{$protocole->_id}}">{{$protocole->_view}}</option>
          {{/foreach}}  
        </select>
      </form>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td>
      <!-- Tabulations -->
      <ul id="main_tab_group" class="control_tabs">
        <li><a href="#div_medicament">Médicaments</a></li>
        <li><a href="#div_dmi">DMI</a></li>
        <li><a href="#div_labo">Labo</a></li>
        <li><a href="#div_imagerie">Imagerie</a></li>
        <li><a href="#div_consult">Consult</a></li>
        <li><a href="#div_kine">Kiné</a></li>
        <li><a href="#div_soin">Soin</a></li>
      </ul>
      <hr class="control_tabs" />
    </td>
  </tr>
</table>

<div id="produits_elements">
  {{include file="inc_vw_produits_elements.tpl"}}  
</div>

{{else}}	
	{{if !$mode_protocole}}
		<form action="?m=dPprescription" method="post" name="addPrescription" onsubmit="return checkForm(this);">
		  <input type="hidden" name="m" value="dPprescription" />
		  <input type="hidden" name="dosql" value="do_prescription_aed" />
		  <input type="hidden" name="prescription_id" value="" />
		  <input type="hidden" name="del" value="0" />
		  <input type="hidden" name="object_class" value="{{$prescription->object_class}}"/>
		  <input type="hidden" name="object_id" value="{{$prescription->object_id}}"/>
		  <select name="praticien_id">
		    {{foreach from=$listPrats item=curr_prat}}
		    <option value="{{$curr_prat->_id}}">
		      {{$curr_prat->_view}}
		    </option>
		    {{/foreach}}
		  </select>
		  <button type="submit" class="new">Créer une prescription</button>
		</form>
  {{/if}}
{{/if}}