{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
selectActivite = function(activite) {
	$$("button.activite").invoke("setStyle", {borderWidth: "1px"} );
  $$("button.ressource").invoke("setStyle", {borderWidth: "1px"} );
	$("trigger-"+activite).setStyle( {borderWidth: "3px"} );
  $$("div.activite").invoke("hide");
  $("activite-"+activite).show();
  PlanningEquipement.hide();
  PlanningTechnicien.hide();
}

selectTechnicien = function(technicien_id) {
  $$("button.ressource").invoke("setStyle", {borderWidth: "1px"} );
  $("technicien-"+technicien_id).setStyle( {borderWidth: "3px"} );
  PlanningEquipement.hide();
  PlanningTechnicien.show(technicien_id);
}

selectEquipement = function(equipement_id) {
  $$("button.ressource").invoke("setStyle", {borderWidth: "1px"} );
  $("equipement-"+equipement_id).setStyle( {borderWidth: "3px"} );
  PlanningEquipement.show(equipement_id);
  PlanningTechnicien.show();
}
</script>

<table class="form">
	<tr><th class="title" colspan="10">Boîte à activités</th></tr>

  <tr>
    <th>{{mb_label object=$bilan field=kine_id}}</th>
    <td><strong>{{mb_value object=$bilan field=kine_id}}</strong></td>
  </tr>

	<tr>
		<th>Activités</th>
		<td>
			{{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
			  {{foreach from=$_lines_by_chap item=_lines_by_cat}}
  		    {{foreach from=$_lines_by_cat.element item=_line name=category}}
            {{if $smarty.foreach.category.first}}
		          {{assign var=category value=$_line->_ref_element_prescription->_ref_category_prescription}}
						  <button id="trigger-{{$category->_guid}}" class="search activite" type="button" onclick="selectActivite('{{$category->_guid}}')">
						    {{$category}}
						  </button>
			      {{/if}}
			    {{/foreach}}
			  {{/foreach}}
			{{/foreach}}
    </td>
	</tr>
	
	<tr>
    <th>Détails</th>
    <td>
      {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
        {{foreach from=$_lines_by_chap item=_lines_by_cat}}
          {{foreach from=$_lines_by_cat.element item=_line name=category}}
            {{assign var=element value=$_line->_ref_element_prescription}}
            {{if $smarty.foreach.category.first}}
              {{assign var=category value=$element->_ref_category_prescription}}
				      <div class="activite" id="activite-{{$category->_guid}}" style="display: none;">
            {{/if}}
              {{mb_include template=inc_vw_line}}
            {{if $smarty.foreach.category.last}}
						  </div>
            {{/if}}
          {{/foreach}}
        {{/foreach}}
      {{/foreach}}
    </td>
  </tr>
  
  <tr>
    <th>Technicien</th>
    <td>
      {{foreach from=$plateau->_ref_techniciens item=_technicien}}
      <button id="technicien-{{$_technicien->_id}}" class="search ressource" type="button" onclick="selectTechnicien('{{$_technicien->_id}}')">
        {{$_technicien}}
      </button>
      {{/foreach}}
    </td>
  </tr>

  <tr>
    <th>Equipement</th>
    <td>
      {{foreach from=$plateau->_ref_equipements item=_equipement}}
      <button id="equipement-{{$_equipement->_id}}" class="search ressource" type="button" onclick="selectEquipement('{{$_equipement->_id}}')">
        {{$_equipement}}
      </button>
      {{/foreach}}
    </td>
  </tr>

</table>
