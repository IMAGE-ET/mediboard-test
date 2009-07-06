{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function popupVoies(){
  url = new Url;
  url.setModuleAction("dPprescription", "vw_voies");
  url.popup(300,700,"Voies");
}

function startAssociation(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_do_add_table_association");
  url.requestUpdate("do_add_association");
}

function exportElementsPrescription(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_export_elements_prescription");
  url.addParam("group_id", $V(document.exportElements.group_id));
  url.requestUpdate("export_elements_prescription");
}

function importElementsPrescription(){
  var url = new Url;
  url.setModuleAction("dPprescription", "import_elements_prescription");
  url.popup(700, 500, "export_elements_prescription");
}

function updateVoie(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_update_voie");
  url.requestUpdate("update_voie");
}

function updateUCD(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_update_ucd");
  url.requestUpdate("update_ucd");
}
</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

	<table class="form">
	  {{assign var="class" value="CPrescription"}}
	  {{assign var="var" value="show_unsigned_lines"}}
	  <tr>
	   <th class="category" colspan="2">
	      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
	      </label>    
	   </th>
	  </tr>
	  <tr>  
	    <td colspan="2" style="text-align: center">
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
	    </td>             
	  </tr>
	  {{assign var="var" value="add_element_category"}}
	  <tr>
	   <th class="category" colspan="2">
	      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
	      </label>    
	   </th>
	  </tr>
	  <tr>  
	    <td colspan="2" style="text-align: center">
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
	    </td>             
	  </tr>
	  {{assign var="var" value="time_alerte_modification"}}
	  <tr>
	    <th colspan="2" class="category">
	      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
	      </label>    
	    </th>
	  </tr>
	  <tr>  
	    <td colspan="2" style="text-align: center">
	      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
	      {{foreach from=$listHours item=_hour}}
	        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}}selected="selected"{{/if}}>
	          {{$_hour}}
	        </option>
	      {{/foreach}}
	      </select>
	      heures
	    </td>             
	  </tr>
	  <!-- Gestion des scores de prescription -->
	  {{assign var="var" value="scores"}}
	  <tr>
	    <th class="category" colspan="2">
	      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
	      </label>    
	    </th>
	  </tr>
		<tr>
	    <th colspan="2" class="category">
	       {{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction{{/tr}}
	    </th>
	  </tr>
	  {{assign var=type_niveau value=interaction}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv1"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv2"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv3"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv4"}}
		<tr>
	    <th colspan="2" class="category">
	       {{tr}}config-{{$m}}-{{$class}}-{{$var}}-posologie{{/tr}}
	    </th>
	  </tr>
	  {{assign var=type_niveau value=posoqte}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv10"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv11"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv12"}}
	  {{assign var=type_niveau value=posoduree}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv20"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv21"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv22"}}
		<tr>
	    <th colspan="2" class="category">
	       {{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil{{/tr}}
	    </th>
	  </tr>
	  {{assign var=type_niveau value=profil}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv0"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv1"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv2"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv9"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv30"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv39"}}
		<tr>
	    <th colspan="2" class="category">
	      Autres
	    </th>
	  </tr>
	  <tr>
	    <th style="width: 50%">
	      <label for="{{$m}}[{{$class}}][{{$var}}][IPC]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-IPC{{/tr}}">
	          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-IPC{{/tr}}
	      </label>  
	    </th>
		  <td style="text-align: center">
		    <select name="{{$m}}[{{$class}}][{{$var}}][IPC]">
	        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.IPC}} selected="selected" {{/if}}>0</option>
	        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.IPC}} selected="selected" {{/if}}>1</option>
	        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.IPC}} selected="selected" {{/if}}>2</option>
	      </select>
		  </td>
		</tr>
		<tr>
		  <th>
	      <label for="{{$m}}[{{$class}}][{{$var}}][allergie]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-allergie{{/tr}}">
	          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-allergie{{/tr}}
	      </label>  
	    </th>
		  <td style="text-align: center">
		    <select name="{{$m}}[{{$class}}][{{$var}}][allergie]">
	        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.allergie}} selected="selected" {{/if}}>0</option>
	        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.allergie}} selected="selected" {{/if}}>1</option>
	        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.allergie}} selected="selected" {{/if}}>2</option>
	      </select>
		  </td>
		</tr>
		<tr>
	    <th>
	      <label for="{{$m}}[{{$class}}][{{$var}}][hors_livret]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-hors_livret{{/tr}}">
	          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-hors_livret{{/tr}}
	      </label>  
	    </th>
		  <td style="text-align: center">
		    <select name="{{$m}}[{{$class}}][{{$var}}][hors_livret]">
	        <option value="0" {{if 0 == $dPconfig.$m.$class.$var.hors_livret}} selected="selected" {{/if}}>0</option>
	        <option value="1" {{if 1 == $dPconfig.$m.$class.$var.hors_livret}} selected="selected" {{/if}}>1</option>
	        <option value="2" {{if 2 == $dPconfig.$m.$class.$var.hors_livret}} selected="selected" {{/if}}>2</option>
	      </select>
		  </td>
		</tr>  
	  {{assign var="class" value="CCategoryPrescription"}}
	  <tr>
	   <th class="category" colspan="2">
	      <label for="{{$m}}[{{$class}}]" title="{{tr}}config-{{$m}}-{{$class}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$class}}{{/tr}}
	      </label>    
	   </th>
	  </tr>
	  <!-- Affichage du header et de la description des chapitres lors de l'impression des ordonnances -->
	  {{mb_include module=system template=inc_config_bool var=show_header}}
	  {{mb_include module=system template=inc_config_bool var=show_description}}
	  <tr> 
      <td colspan="2">
	      <table class="form">
	        <th class="category">Chapitre</th>
	       	<th class="category">Impression ordonnance</th>
	        <th class="category">Unité de prise</th>
				  {{include file="inc_configure_chapitre.tpl" var=dmi}}
				  {{include file="inc_configure_chapitre.tpl" var=anapath}}
				  {{include file="inc_configure_chapitre.tpl" var=biologie}}
				  {{include file="inc_configure_chapitre.tpl" var=imagerie}}
				  {{include file="inc_configure_chapitre.tpl" var=consult}}
				  {{include file="inc_configure_chapitre.tpl" var=kine}}
				  {{include file="inc_configure_chapitre.tpl" var=soin}}
				  {{include file="inc_configure_chapitre.tpl" var=dm}}
	      </table>
	    </td>
	  </tr>
	  
	  {{assign var="class" value="CMomentUnitaire"}}
	  {{assign var="var" value="principaux"}}
	  <tr>
	   <th class="category" colspan="2">
	      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
	      </label>    
	   </th>
	  </tr>
	  <tr>  
	    <td colspan="2" style="text-align: center">
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
	    </td>             
	  </tr>
	  <tr>
	    <th class="category" colspan="10">Affichage des voies disponibles</th>
	  </tr>
	  <tr>
	    <td colspan="2" style="text-align: center;">
	      <button class="search" onclick="popupVoies()" type="button">Afficher les voies</button>
	    </td>
	  </tr>
	  
	  <!-- Handler -->  
	  {{include file="../../system/templates/configure_handler.tpl" class_handler=CPrescriptionLineHandler}}   
	  
	  {{assign var=class value="CAdministration"}}
	  {{assign var=var value="hors_plage"}}
	  <tr>
	    <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}} en dehors des plages prevues</th>
	  </tr>
	  <tr>  
	    <td colspan="2" style="text-align: center">
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
	    </td>             
	  </tr>  
	  <tr>
	    <td class="button" colspan="2">
	      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
	    </td>
	  </tr>
	</table>
</form>
<hr />
<!-- Imports/Exports -->
<table class="tbl">
  <tr>
    <th class="title" colspan="2">Opération d'imports et exports</th>
  </tr>
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  <tr>
    <td><button class="tick" onclick="startAssociation()" >Importer la table d'association</button></td>
    <td id="do_add_association"></td>
  </tr>
  <tr>
    <td>
	    <button class="tick" onclick="exportElementsPrescription()" >Exporter les elements de prescriptions</button>
	    <form name="exportElements">
	      <select name="group_id">
	        <option value="no_group">Non associées</option>
	        {{foreach from=$groups item=_group}}
	          <option value="{{$_group->_id}}">de {{$_group->_view}}</option>
	        {{/foreach}}
	      </select>
      </form>
    </td>
    <td id="export_elements_prescription"></td>
  </tr>
  <tr>
    <td colspan="2"><button class="tick" onclick="importElementsPrescription()" >Importer les elements de prescriptions</button></td>
  </tr>
  <tr>
    <td><button class="tick" onclick="updateVoie()">Mettre à jour la voie pour les lignes de medicaments</button></td>
    <td id="update_voie"></td>
  </tr>
  <tr>
    <td><button class="tick" onclick="updateUCD()">Mettre à jour les code UCD et CIS</button></td>
    <td id="update_ucd"></td>
  </tr>
</table>