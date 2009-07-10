{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function showLegend() {
  url = new Url("dPurgences", "vw_legende");
  url.popup(300, 320, "Legende");
}

// Fonction de refresh du temps d'attente
function updateAttente(sejour_id){
  var url = new Url("dPurgences", "httpreq_vw_attente");
  url.addParam("sejour_id", sejour_id);
  url.periodicalUpdate('attente-'+sejour_id, { frequency: 60, waitingText: null } );
}

// fonction de refresh periodique de la main courante
function updateMainCourante(){
  var url = new Url("dPurgences", "httpreq_vw_main_courante");
  url.periodicalUpdate('main_courante', { frequency: 60, waitingText: null } );
}
 
function updateConsultations(){
  var url = new Url("dPcabinet", "vw_journee");
  url.addParam("date", "{{$date}}");
  url.addParam("mode_urgence", true);
  url.periodicalUpdate('consultations', { frequency: 60, waitingText: null } );
} 
 
function printMainCourante() {
  var url = new Url("dPurgences", "print_main_courante");
  url.addParam("date", "{{$date}}");
  url.popup(800, 600, "Impression main courante");
}

Main.add(function () {
  updateMainCourante();
  updateConsultations();
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
  
  var tabs = Control.Tabs.create('tab_main_courante', true);
});

</script>


<ul id="tab_main_courante" class="control_tabs">
  <li><a href="#_main_courante">Main courante</a></li>
  <li><a href="#consultations">Consultations</a></li>
</ul>
<hr class="control_tabs" />

<div id="_main_courante">
	<table style="width:100%">
	  <tr>
	    <td>
	      <a class="button new" href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id=0">
	        Ajouter un patient
	      </a> 
	    </td>
	    <th>
	     le
	     {{$date|date_format:$dPconfig.longdate}}
	      <form action="?" name="changeDate" method="get">
	        <input type="hidden" name="m" value="{{$m}}" />
	        <input type="hidden" name="tab" value="{{$tab}}" />
	        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
	      </form>
	    </th>
	    <td style="text-align: right">
	     Type d'affichage
	     <form name="selView" action="?m=dPurgences&amp;tab=vw_idx_rpu" method="post">
		      <select name="selAffichage" onchange="submit();">
		        <option value="tous" {{if $selAffichage == "tous"}}selected = "selected"{{/if}}>Tous</option>
		        <option value="presents" {{if $selAffichage == "presents"}} selected = "selected" {{/if}}>Présents</option>
		        <option value="prendre_en_charge" {{if $selAffichage == "prendre_en_charge"}} selected = "selected" {{/if}}>A prendre en charge</option>
		      </select>
		    </form>
	      <a href="#" onclick="printMainCourante()" class="button print">Main courante</a>
	      <a href="#" onclick="showLegend()" class="button search">Légende</a>
	    </td>
	  </tr>
	</table>
	<div id="main_courante"></div>
</div>

<div id="consultations">

</div>