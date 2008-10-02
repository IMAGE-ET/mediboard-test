{{if $pack->_id}}
  <form name="addDelProtocoleToPack" action="?" method="post">
	  <input type="hidden" name="dosql" value="do_prescription_protocole_pack_item_aed" />
		<input type="hidden" name="m" value="dPprescription" />
		<input type="hidden" name="del" value="0" />
		<input type="hidden" name="prescription_protocole_pack_id" value="{{$pack->_id}}" /> 
		<input type="hidden" name="prescription_protocole_pack_item_id" value="" />    
		<input type="hidden" name="prescription_id" value="" />
  </form>		     
  
	<table class="tbl">
	  <tr>
	    <th colspan="2" class="title">
	      <form name="editLibellePack" action="?" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: function() { Protocole.refreshListPack('{{$pack->_id}}'); } } )">
	        <input type="hidden" name="dosql" value="do_prescription_protocole_pack_aed" />
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="del" value="0" />
	        <input type="hidden" name="prescription_protocole_pack_id" value="{{$pack->_id}}" />
	        {{mb_field object=$pack field="libelle"}}
	        <button class="tick notext" onclick="this.form.onsubmit()"></button>  
	      </form>
	      <button type="button" class="search" onclick="Protocole.previewPack('{{$pack->_id}}')">Visualiser</button>
	    </th>
	  </tr>
	  <tr>
	    <th colspan="2">Protocoles disponibles</th>
	  </tr>
	  <!-- Ajout de protocoles dans le pack -->
	  <tr>
	    <td>
	      Praticien: 
	     {{if $protocoles_praticien|@count}}
		     <select name="protocole_prat_id" onchange="if(this.value) { Protocole.addProtocoleToPack(this.value); }">
		       <option value="">&mdash; Protocoles du praticien</option>
		     {{foreach from=$protocoles_praticien key=object_class item=_protocoles_praticien_by_class}}
		       {{foreach from=$_protocoles_praticien_by_class item=_protocole_praticien}}
		         <option value="{{$_protocole_praticien->_id}}">{{$_protocole_praticien->_view}}</option>
		       {{/foreach}}
		     {{/foreach}}
		     </select>
	     {{else}}
	     Aucun protocole disponible
	     {{/if}}
	    </td>
	    <td>
	      Cabinet: 
	      {{if $protocoles_function|@count}}
	     <select name="protocole_func_id" onchange="if(this.value) { Protocole.addProtocoleToPack(this.value); }">
	     <option value="">&mdash; Protocoles du cabinet</option>
	     {{foreach from=$protocoles_function key=object_class item=_protocoles_function_by_class}}
	       {{foreach from=$_protocoles_function_by_class item=_protocole_function}}
	         <option value="{{$_protocole_function->_id}}">{{$_protocole_function->_view}}</option>
	       {{/foreach}}
	     {{/foreach}}
	     </select>
	     {{else}}
	     Aucun protocole disponible
	     {{/if}}
	    </td>
	  </tr>
	  <!-- Affichage des protocoles du packs -->
	  <tr>
	    <th colspan="2">Protocoles utilisés dans le pack sélectionné</th>
	  </tr>
	  <tr>
	    <td colspan="2">
	      <ul>
	    {{foreach from=$pack->_ref_protocole_pack_items item=_item_pack}}
	      <li><button type="button" class="cancel notext" onclick="Protocole.delProtocoleToPack('{{$_item_pack->_id}}')"></button> {{$_item_pack->_view}}</li>
	    {{/foreach}}
	    </ul>
	    </td>
	  </tr>
	</table>
	<!-- Affichage du pack -->
  {{include file="inc_vw_prescription.tpl" mode_protocole=0 mode_pharma=0}}
{{else}}
	<table class="form">
	  <tr>
	    <th class="category">Création d'un pack</th>
	  </tr>
	 <tr>
	   <td>
	  <form name="createPack" action="?" method="post" onsubmit="Protocole.addPack();">
	    <input type="hidden" name="m" value="dPprescription" />
	    <input type="hidden" name="dosql" value="do_prescription_protocole_pack_aed" />
	    <input type="hidden" name="prescription_protocole_pack_id" value="" />
	    <input type="hidden" name="praticien_id" value="" />
	    <input type="hidden" name="function_id" value="" />	
	    <input type="hidden" name="callback" value="Protocole.reloadAfterAddPack" />
	    
	    <table class="form">
	      <tr>
	        <th style="width: 50%">{{mb_label object=$pack field="libelle"}}</th>
	        <td>{{mb_field object=$pack field="libelle"}}</td>
	      </tr>
	      <tr>
	        <th>{{mb_label object=$pack field="object_class"}}</th>
	        <td>{{mb_field object=$pack field="object_class"}}</td>  
	      </tr>
        <tr>
          <td colspan="2" style="text-align: center"><button type="button" class="submit" onclick="this.form.onsubmit()">Créer</button></td>
        </tr>
	  </form>
	  <script type="text/javascript">
      prepareForm("createPack");
     </script>
	   </td>
	  </tr>
	</table>	       
{{/if}}

