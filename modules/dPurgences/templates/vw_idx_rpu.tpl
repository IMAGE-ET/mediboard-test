{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$group->service_urgences_id}}
  <div class="small-warning">{{tr}}dPurgences-no-service_urgences_id{{/tr}}</div>
{{else}}
<script type="text/javascript">

function showLegend() {
  var url = new Url("dPurgences", "vw_legende");
  url.popup(300, 320, "Legende");
}

// fonction de refresh periodique de la main courante
function updateMainCourante() {
  var url = new Url("dPurgences", "httpreq_vw_main_courante");
  url.periodicalUpdate('main_courante', { frequency: 60 } );
}

function updateIdentitoVigilance() {
  var url = new Url("dPurgences", "ajax_identito_vigilance");
  url.periodicalUpdate('identito_vigilance', { frequency: 60 } );
}
 
function updateConsultations() {
  var url = new Url("dPcabinet", "vw_journee");
  url.addParam("date", "{{$date}}");
  url.addParam("mode_urgence", true);
  url.periodicalUpdate('consultations', { frequency: 60 } );
} 
 
function printMainCourante() {
  var url = new Url("dPurgences", "print_main_courante");
  url.addParam("date", "{{$date}}");
  url.popup(800, 600, "Impression main courante");
}

function filterPatient(input) {
  $$("#main_courante tr").invoke("show");
  
  var term = $V(input);
  if (!term) return;
  
  $$("#main_courante .CPatient-view").each(function(p) {
    if (!p.innerHTML.like(term)) {
      p.up("tr").hide();
    }
  });
}

onMergeComplete = function() {
  updateMainCourante();
	updateIdentitoVigilance()
}

Main.add(function () {
  updateMainCourante();
  updateConsultations();
  updateIdentitoVigilance();
	
  Calendar.regField(getForm("changeDate").date, null, { noView: true } );
  
  Control.Tabs.create('tab_main_courante', true);
});

</script>

<ul id="tab_main_courante" class="control_tabs">
  <li style="float: right">
    <form action="?" name="FindSejour" method="get">
      <label for="sip_barcode" title="Veuillez doucher le numéro de dossier sur un document ou bien le saisir à la main">
        Code à barres de dossier
      </label>
        
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="text" name="sip_barcode" onchange="this.form.submit()" />
      
      <button type="submit" class="search notext">{{tr}}Search{{/tr}}</button>
    </form>
  </li>
  <li><a href="#holder_main_courante">Main courante <small>(0)</small></a></li>
  <li><a href="#consultations" class="empty">Reconvocations</a></li>
  <li><a href="#identito_vigilance" class="empty">Identito-vigilance</a></li>
</ul>
<hr class="control_tabs" />

<div id="holder_main_courante">
	<table style="width: 100%;">
	  <tr>
	    <td style="white-space: nowrap; width: 1%;">
	      <a class="button new" href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id=0">
	        Ajouter un patient
	      </a>
	    </td>
	    <th style="font-size: 1.2em;">
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
		      <select name="selAffichage" onchange="this.form.submit();">
		        <option value="tous" {{if $selAffichage == "tous"}}selected = "selected"{{/if}}>Tous</option>
		        <option value="presents" {{if $selAffichage == "presents"}} selected = "selected" {{/if}}>Présents</option>
		        <option value="prendre_en_charge" {{if $selAffichage == "prendre_en_charge"}} selected = "selected" {{/if}}>A prendre en charge</option>
		        <option value="annule_hospitalise" {{if $selAffichage == "annule_hospitalise"}} selected = "selected" {{/if}}>Annulé et Hospitalisé</option>
		      </select>
		    </form>
	      <a href="#" onclick="printMainCourante()" class="button print">Main courante</a>
	      <a href="#" onclick="showLegend()" class="button search">Légende</a>
	    </td>
	  </tr>
	  <tr>
	    <td></td>
	    <td>
        Filtrer par nom : 
        <input type="text" onkeyup="filterPatient(this)" id="filter-patient-name" />
      </td>
      <td style="text-align: right;">
	      <button type="button" class="warning" onclick="$$('.veille').invoke('toggle')" style="display: none;">
	        Afficher les <span class="count">0</span> admissions de la veille
        </button>
      </td>
	  </tr>
	</table>
  
	<div id="main_courante"></div>
</div>

<div id="consultations" style="display: none;"></div>

<div id="identito_vigilance" style="display: none;"></div>
{{/if}}