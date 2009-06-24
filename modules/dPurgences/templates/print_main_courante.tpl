{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <th>
      <a href="#print" onclick="window.print()">
        Main courante du {{$date|date_format:"%A %d %b %Y"}}
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
			    <th>{{mb_label class=CRPU field=ccmu       }}</th>
			    <th>{{mb_label class=CRPU field=_patient_id}}</th>
			    <th>{{mb_label class=CRPU field=_entree    }}</th>
			    <th>{{tr}}CRPU-_responsable_id{{/tr}}</th>
			    <th>{{tr}}CRPU-diag_infirmier{{/tr}}</th>
		  	  <th>Prise en charge</th>
		  	</tr>
		  	
			  {{foreach from=$listSejours item=sejour}}
			  {{assign var=rpu value=$sejour->_ref_rpu}}
			  {{assign var=patient value=$sejour->_ref_patient}}
			  {{assign var=consult value=$rpu->_ref_consult}}
			  <tr>
			    <td>
			    {{if $rpu->ccmu}}
			      {{tr}}CRPU.ccmu.{{$rpu->ccmu}}{{/tr}}
			    {{/if}}
			    </td>
			    <td>{{$sejour->_ref_patient->_view}}</td>
			    <td>{{$sejour->_entree|date_format:$dPconfig.datetime}}</td>
			    <td>{{$sejour->_ref_praticien->_view}}</td>
			    <td>{{$rpu->diag_infirmier|nl2br}}</td>
			    <td>
			    {{if $consult->_id}}
			    {{$consult->_ref_plageconsult->_ref_chir->_view}}
			    {{/if}}
			    </td>
			  </tr>
			  {{/foreach}}
			</table>
	  </td>
  </tr>
</table>