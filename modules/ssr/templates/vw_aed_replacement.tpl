{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=planification}}
{{mb_include_script module="ssr" script="planning"}}
<script type="text/javascript">

Planification.onCompleteShowWeek = function(){
  refreshlistSejour('','kine');
  refreshlistSejour('','reeducateur');
  $('replacement-kine').update('');
  $('replacement-reeducateur').update(''); 
}

refreshlistSejour = function(selected_sejour_id, type){
  var url = new Url("ssr", "ajax_vw_list_sejours");
	url.addParam("type", type);
	url.requestUpdate("sejours-"+type, { 
	  onComplete: function(){
		  $('replacement-'+type+'-'+selected_sejour_id).addUniqueClassName('selected');
		}
	});
}

refreshReplacement = function(sejour_id, conge_id, type){
  var url = new Url("ssr", "ajax_vw_replacement");
  url.addParam("sejour_id", sejour_id);
  url.addParam("conge_id", conge_id);
	url.addParam("type", type);
  url.requestUpdate("replacement-"+type);
}

refreshReplacerPlanning = function(replacer_id){
  var url = new Url("ssr", "ajax_planning_technicien");
	url.addParam("kine_id", replacer_id);
	url.requestUpdate("replacer-planning");
}

printRepartition = function(){
  var url = new Url("ssr", "vw_idx_repartition");
	url.addParam("readonly", true);
	url.popup("Repartition des patients");
}

Main.add(function(){
  Planification.showWeek();
	Planification.onCompleteShowWeek();
	
	Control.Tabs.create('tabs-replacement', true);
  
  var kines = $("kines");
  var height = (document.viewport.getHeight() - kines.cumulativeOffset().top - 10) + "px";
  kines.down("td").style.height = height;
  $("reeducateurs").down("td").style.height = height;
});

</script>

<table class="main">
	<tr style="height: 0.1%;">
    <td id="week-changer" colspan="2"></td>
  </tr>
	<tr style="height: 0.1%;">
	  <td colspan="2">
	  	<div id="toto">
	  	  <ul id="tabs-replacement" class="control_tabs">
				  <li>
				    <a href="#kines">Remplacement des référents</a>
				  </li>
				  <li>
				    <a href="#reeducateurs">Transfert des rééducateurs</a>
				  </li>
					<li style="float: right;">
						<button type="button" onclick="printRepartition();" class="print">Repartition patients</button>
					</li>
			  </ul>
			   <hr class="control_tabs" />
      </div>
	  </td>
	</tr>
	<tr id="kines">	
		<td class="halfPane" style="vertical-align: top;">
      <div style="overflow: auto; height: 100%;">
		    <div id="sejours-kine"></div>
      </div>
		</td>
		<td id="replacement-kine"><td>
	</tr>
  <tr id="reeducateurs">
    <td class="halfPane" style="vertical-align: top;">
      <div style="overflow: auto; height: 100%;">
        <div id="sejours-reeducateur"></div>
      </div>
    </td>
    <td id="replacement-reeducateur"></td>
  </tr>
</table>