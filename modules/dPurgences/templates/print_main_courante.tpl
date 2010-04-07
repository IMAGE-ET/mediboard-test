{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main" style="font-size: 1.2em;">
  <tr>
    <th>
      <a href="#print" onclick="window.print()">
        Main courante du {{$date|date_format:"%A %d %b %Y"}}<br /> Total: {{$listSejours|@count}} RPU
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>{{mb_label class=CRPU field=_entree }}</th>
				  <th>{{mb_label class=CRPU field=_patient_id}}</th>
				  <th>{{mb_label class=CRPU field=ccmu}}</th>
					<th>{{mb_label class=CRPU field=diag_infirmier}}</th>
					<th>Heure PeC</th>
				  <th>{{mb_label class=CRPU field=_responsable_id}}</th>  
					<th>{{mb_label class=CSejour field=mode_sortie}} / {{mb_label class=CRPU field=orientation}}</th>
				  <th>{{mb_label class=CRPU field=_sortie}}</th>
				</tr>
			  {{foreach from=$listSejours item=sejour}}
			  {{assign var=rpu value=$sejour->_ref_rpu}}
			  {{assign var=patient value=$sejour->_ref_patient}}
			  {{assign var=consult value=$rpu->_ref_consult}}
			  <tr>
			  {{if $rpu->_id}}
			  	<td>{{mb_value object=$rpu field="_entree"}}</td>
			    <td>{{mb_value object=$sejour field="patient_id"}}</td>
					<td>{{mb_value object=$rpu field="ccmu"}}</td>
					<td>{{mb_value object=$rpu field="diag_infirmier"}}</td>    
					<td>{{mb_value object=$consult field="heure"}}</td>      
			    <td>{{mb_value object=$sejour field="praticien_id"}}</td>
					<td>
						{{if $sejour->mode_sortie}}
							{{mb_value object=$sejour field="mode_sortie"}}
						{{/if}}
					  {{if $sejour->mode_sortie == "transfert"}}
						  <br />
						  {{mb_value object=$sejour field="etablissement_transfert_id"}}
						{{/if}}
						{{if $rpu->orientation}}
							<br />
						  {{mb_value object=$rpu field="orientation"}}
						{{/if}}
					</td>
					<td>{{mb_value object=$rpu field="_sortie"}}</td>
			  {{else}}
					<!-- Pas de RPU pour ce séjour d'urgence -->
					<td colspan="10">
					  <div class="small-warning">
					  	Ce séjour d'urgence n'est pas associé à un RPU.
					  </div>
					</td>
				{{/if}}
				</tr>
			  {{/foreach}}
			</table>
	  </td>
  </tr>
</table>