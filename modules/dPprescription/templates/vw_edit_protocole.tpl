{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPprescription" script="protocole"}}

<script type="text/javascript">

Main.add( function(){
  // Refesh de la liste des protocoles
  Protocole.refreshList('{{$protocole_id}}');
  {{if $protocole_id}}
  Prescription.reload('{{$protocole_id}}', '', '', '1');
  {{/if}}
} );

</script>

<table class="main">
  <tr>
    <!-- Affichage de la liste des protocoles pour le praticien selectionné -->
    <td class="halfPane" style="width: 330px;">
	    <form name="selPrat" action="?" method="get">
	      <input type="hidden" name="tab" value="vw_edit_protocole" />
        <input type="hidden" name="m" value="dPprescription" />
        <select name="praticien_id" onchange="this.form.function_id.value=''; this.form.group_id.value=''; Protocole.refreshListProt();">
          <option value="">&mdash; Choix d'un praticien</option>
	        {{foreach from=$praticiens item=praticien}}
	        <option class="mediuser" 
	                style="border-color: #{{$praticien->_ref_function->color}};" 
	                value="{{$praticien->_id}}"
	                {{if $praticien->_id == $praticien_id}}selected="selected"{{/if}}>{{$praticien->_view}}
	        </option>
	        {{/foreach}}
	      </select>
	      <select name="function_id" onchange="this.form.praticien_id.value=''; this.form.group_id.value=''; Protocole.refreshListProt();">
          <option value="">&mdash; Choix du cabinet</option>
          {{foreach from=$functions item=_function}}
          <option class="mediuser" style="border-color: #{{$_function->color}}" value="{{$_function->_id}}" {{if $function_id == $_function->_id}}selected=selected{{/if}}>{{$_function->_view}}</option>
          {{/foreach}}
        </select>
        <select name="group_id" onchange="this.form.function_id.value=''; this.form.praticien_id.value=''; Protocole.refreshListProt();">
          <option value="">&mdash; Choix d'un établissement</option>
          {{foreach from=$groups item=_group}}
          <option value="{{$_group->_id}}" {{if $group_id == $_group->_id}}selected=selected{{/if}}>{{$_group->_view}}</option>
          {{/foreach}}
        </select>
        <br />
	      <button type="button" class="submit" onclick="this.form.submit();">
	        Créer un protocole
	      </button>
	    </form>
	    <div id="protocoles"></div>
    </td>
    <!-- Affichage du protocole sélectionné-->
    <td>
      <div id="vw_protocole">
        {{if !$protocole_id}}
		    {{include file="inc_vw_prescription.tpl" httpreq=1 mode_protocole=1 prescription=$protocole category="medicament"}}
		    {{/if}}
		  </div>  
    </td>
  </tr>
</table>