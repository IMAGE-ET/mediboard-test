{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
	    <td style="text-align: center">
	      <select name="protocole_id" onchange="if(this.value) { Protocole.addProtocoleToPack(this.value); }">
	        <option value="">&mdash; Choix d'un protocole</option>
  	      {{foreach from=$protocoles key=owner item=_protocoles_by_owner}}
  				  {{if $_protocoles_by_owner|@count}}
  				    <optgroup label="Liste des protocoles {{tr}}CPrescription._owner.{{$owner}}{{/tr}}">
    				  {{foreach from=$_protocoles_by_owner item=_protocoles_by_type key=type}}
    				    <optgroup label="{{tr}}CPrescription.type.{{$type}}{{/tr}}" style="padding-left: 10px;">
    				    {{foreach from=$_protocoles_by_type item=protocole}}
    				      <option value="{{$protocole->_id}}">{{$protocole->libelle}}</option>
    				    {{/foreach}}
    				    </optgroup>
    				  {{/foreach}}
              </optgroup>
  				  {{/if}}
  			  {{/foreach}}
	      </select>
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
  {{if $praticien_id || $function_id}}
		<table class="form">
		  <tr>
		    <th class="category">Création d'un pack</th>
		  </tr>
		 <tr>
		   <td>
		  <form name="createPack" action="?" method="post" onsubmit="Protocole.addPack();" class="{{$pack->_spec}}">
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
		   </td>
		  </tr>
		</table>	 
	{{else}}
		<div class="small-info">
		  Veuillez sélectionner un praticien ou cabinet pour créer un pack de protocole.
		</div>
	{{/if}}      
{{/if}}

