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
	
	Control.Tabs.create('tabs-replacement', true).activeLink.onmouseup();
});

</script>

<table class="main">
	<tr style="height: 0.1%;">
    <td id="week-changer" colspan="2"></td>
  </tr>
	<tr style="height: 0.1%;">
	  <td colspan="2">
  	  <ul id="tabs-replacement" class="control_tabs">
			  <li>
			    <a class="empty" href="#kines" onmouseup="ViewPort.SetAvlHeight.defer('sejours-kine', 1);">
			    	Remplacement des référents
						<small>(-)</small>
					</a>
			  </li>
			  <li>
			    <a class="empty" href="#reeducateurs" onmouseup="ViewPort.SetAvlHeight.defer('sejours-reeducateur', 1);">
			    	Transfert des rééducateurs
            <small>(-)</small>
					</a>
			  </li>
        <li>
          <a class="empty" href="#hors-bornes" onmouseup="ViewPort.SetAvlHeight.defer('sejours-hors-bornes', 1);">
            Planifications hors-séjours
            <small>(-)</small>
          </a>
        </li>
				<li style="float: right;">
					<button type="button" onclick="printRepartition();" class="print">Repartition patients</button>
				</li>
		  </ul>
		   <hr class="control_tabs" />
	  </td>
	</tr>
	<tr id="kines">	
		<td class="halfPane" style="vertical-align: top;">
	    <div id="sejours-kine"></div>
		</td>
		<td id="replacement-kine"></td>
	</tr>
  <tr id="reeducateurs">
    <td class="halfPane" style="vertical-align: top;">
      <div id="sejours-reeducateur"></div>
    </td>
    <td id="replacement-reeducateur"></td>
  </tr>
	
  <tr id="hors-bornes">
    <td class="halfPane" style="vertical-align: top;">
      <div id="sejours-hors-bornes"></div>
		</td>
		<td>
			
		</td>
  </tr>	
</table>