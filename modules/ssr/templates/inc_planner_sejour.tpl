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
		<th>Activités</th>
		<td>
			{{foreach from=$bilan->_activites item=_activite}}
			{{if $bilan->$_activite}}
	    <button id="trigger-{{$_activite}}" class="search activite" type="button" onclick="selectActivite('{{$_activite}}')">
	    	{{mb_title object=$bilan field=$_activite}}
			</button>
			{{/if}}		
			{{/foreach}}
    </td>
	</tr>
	
	<tr>
    <th>Détails</th>
    <td>
      {{foreach from=$bilan->_activites item=_activite}}
      {{if $bilan->$_activite}}
      <div class="activite" id="activite-{{$_activite}}" style="display: none;">
      	<strong>{{mb_value object=$bilan field=$_activite}}</strong></div>
      {{/if}}   
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
