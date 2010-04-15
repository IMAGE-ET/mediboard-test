{{* $Id: vw_idx_rpu.tpl 6473 2009-06-24 15:18:19Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $isImedsInstalled}}
  {{mb_include_script module="dPImeds" script="Imeds_results_watcher"}}
{{/if}}

<script type="text/javascript">
	function refreshAttente(debut, fin, rpu_id) {
	  var url = new Url("dPurgences", "ajax_vw_attente");
	  url.addParam("rpu_id", rpu_id);
		url.addParam("debut", debut);
		url.addParam("fin", fin);
	  url.addParam("attente", 1);
	  url.requestUpdate(fin+'-'+rpu_id);
  }
		
	Main.add(function () {
    Calendar.regField(getForm("changeDate").date, null, {noView: true});
		
		{{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}
  });
</script>

<table style="width:100%">
  <tr>
    <th>
     le
     <big>{{$date|date_format:$dPconfig.longdate}}</big>
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
</table>

<table class="tbl">
	<tr>
		<th class="title" rowspan="2">{{mb_title class=CRPU field="_patient_id"}}</th>
		<th class="title" rowspan="2">{{mb_title class=CRPU field="_responsable_id"}}</th>
		<th class="title" colspan="2">{{tr}}CRPU-radio{{/tr}}</th>
		<th class="title" colspan="2">{{tr}}CRPU-bio{{/tr}}</th>
		<th class="title" colspan="2">{{tr}}CRPU-specia{{/tr}}</th>
  </tr>
  <tr>
    <th>{{mb_title class=CRPU field="radio_debut"}}</th>
    <th>{{mb_title class=CRPU field="radio_fin"}}</th>
    <th>{{mb_title class=CRPU field="bio_depart"}}</th>
    <th>{{mb_title class=CRPU field="bio_retour"}}</th>
		<th>{{mb_title class=CRPU field="specia_att"}}</th>
    <th>{{mb_title class=CRPU field="specia_arr"}}</th>
  </tr>
  {{foreach from=$listSejours item=_sejour}}
    {{assign var=rpu value=$_sejour->_ref_rpu}}
		{{assign var=rpu_id value=$rpu->_id}}
    {{assign var=patient value=$_sejour->_ref_patient}}
		{{assign var=rpu_link value="?m=dPurgences&tab=vw_aed_rpu&rpu_id=$rpu_id"}}
			
		<tr style="text-align: center;{{if $_sejour->sortie_reelle}}opacity: 0.6{{/if}}">			
			<td>
			  <a style="float: right;" title="Voir le dossier" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}&amp;sejour_id={{$_sejour->_id}}">
			    <img src="images/icons/search.png" alt="Dossier patient"/>
			  </a>
			  <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$rpu->_id}}">
			    <strong>
			    {{$patient->_view}}
			    </strong>
			    <br />{{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}}
			  </a>
			</td>
			
			<td>
			  <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$rpu->_id}}">
			    {{$_sejour->_ref_praticien->_view}}
			  </a>
			</td>
			
			{{mb_include module=dPurgences template=inc_vw_attente debut=radio_debut fin=radio_fin}}
			
			{{mb_include module=dPurgences template=inc_vw_attente debut=bio_depart fin=bio_retour}}
			
			{{mb_include module=dPurgences template=inc_vw_attente debut=specia_att fin=specia_arr}}
		</tr>
  {{/foreach}}
</table>
