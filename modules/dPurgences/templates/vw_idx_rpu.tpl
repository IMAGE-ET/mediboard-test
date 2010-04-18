{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPurgences script=main_courante}}
{{mb_include_script module=dPurgences script=identito_vigilance}}
{{if $isImedsInstalled}}
  {{mb_include_script module="dPImeds" script="Imeds_results_watcher"}}
{{/if}}

{{if !$group->service_urgences_id}}
  <div class="small-warning">{{tr}}dPurgences-no-service_urgences_id{{/tr}}</div>
{{else}}

<script type="text/javascript">
 
function updateConsultations(frequency) {
  var url = new Url("dPcabinet", "vw_journee");
  url.addParam("date", "{{$date}}");
  url.addParam("mode_urgence", true);
  url.periodicalUpdate('consultations', { frequency: frequency } );
} 
 
onMergeComplete = function() {
  IdentitoVigilance.start(0, 80);
  MainCourante.start(1, 60);
}

Main.add(function () {
  // Delays prevent potential overload with periodical previous updates
  MainCourante.start(0, 60);
  updateConsultations.delay(1, 80);
  IdentitoVigilance.start(2,100);

  var tabs = Control.Tabs.create('tab_main_courante', false);
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
      <input type="text" size="5" name="sip_barcode" onchange="this.form.submit()" />
      
      <button type="submit" class="search notext">{{tr}}Search{{/tr}}</button>
    </form>
  </li>
  <li><a href="#holder_main_courante">Main courante <small>(&ndash;)</small></a></li>
  <li><a href="#consultations" class="empty">Reconvocations <small>(&ndash; / &ndash;)</small></a></li>
  <li><a href="#identito_vigilance" class="empty">Identito-vigilance <small>(&ndash;)</small></a></li>
  <li style="width: 20em; text-align: center">
    <script type="text/javascript">
    Main.add(function() {
      Calendar.regField(getForm("changeDate").date, null, { noView: true } );
    } );
    </script>
    <strong><big>{{$date|date_format:$dPconfig.longdate}}</big></strong>
    
    <form action="?" name="changeDate" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
    </form>
  </li>
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
			
	    <td style="text-align: right;">
        <label style="visibility: hidden;" class="count" title="Cacher les admissions non-sorties des {{$dPconfig.dPurgences.date_tolerance}} derniers jours">
  	      <input type="checkbox" onchange="$$('.veille').invoke('setVisible', !this.checked)" />
  	      Cacher les <span>0</span> admission(s) antérieure(s)
        </label>
			</td>

	    <td style="text-align: right">
	     Affichage
	     <form name="selView" action="?m=dPurgences&amp;tab=vw_idx_rpu" method="post">
		      <select name="selAffichage" onchange="this.form.submit();">
		        <option value="tous" {{if $selAffichage == "tous"}}selected = "selected"{{/if}}>Tous</option>
		        <option value="presents" {{if $selAffichage == "presents"}} selected = "selected" {{/if}}>Présents</option>
		        <option value="prendre_en_charge" {{if $selAffichage == "prendre_en_charge"}} selected = "selected" {{/if}}>A prendre en charge</option>
		        <option value="annule_hospitalise" {{if $selAffichage == "annule_hospitalise"}} selected = "selected" {{/if}}>Annulé et Hospitalisé</option>
		      </select>
		    </form>
	      <a href="#" onclick="MainCourante.print('{{$date}}')" class="button print">Main courante</a>
	      <a href="#" onclick="MainCourante.legend()" class="button search">Légende</a>
	    </td>
	  </tr>
	</table>
  
	<div id="main_courante"></div>
</div>

<div id="consultations" style="display: none;">
  <div class="small-info">{{tr}}msg-common-loading-soon{{/tr}}</div>
</div>

<div id="identito_vigilance" style="display: none; margin: 0 5px;">
  <div class="small-info">{{tr}}msg-common-loading-soon{{/tr}}</div>
</div>


{{/if}}