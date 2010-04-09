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
    	{{if $offline}}
	      <span style="float: right">
	        {{$dateTime|date_format:$dPconfig.datetime}}
	      </span>
	    {{/if}}
       <a href="#print" onclick="window.print()">
        Main courante du {{$date|date_format:$dPconfig.longdate}}
				<br /> Total: {{$listSejours|@count}} RPU
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th colspan="2">{{mb_title class=CRPU field=_entree}}</th>
				  <th>{{mb_label class=CRPU field=_patient_id}}</th>
				  <th>{{mb_label class=CRPU field=ccmu}}</th>
					<th>{{mb_label class=CRPU field=diag_infirmier}}</th>
					<th>Heure PeC</th>
				  <th>{{mb_label class=CRPU field=_responsable_id}}</th>  
					<th>
						{{mb_label class=CSejour field=mode_sortie}} 
						<br/> &amp; 
						{{mb_label class=CRPU field=orientation}}
					</th>
				  <th colspan="2">{{mb_title class=CRPU field=_sortie}}</th>
				</tr>
			  {{foreach from=$listSejours item=sejour}}
			  {{assign var=rpu value=$sejour->_ref_rpu}}
			  {{assign var=patient value=$sejour->_ref_patient}}
			  {{assign var=consult value=$rpu->_ref_consult}}
			  <tr>
					<td style="text-align: center">
            {{mb_ditto value=$sejour->_entree|date_format:$dPconfig.date name=entree}}
          </td>
          <td>{{$sejour->_entree|date_format:$dPconfig.time}}</td>
			    <td class="text">
						{{if $offline && $rpu->_id}}
	            <button class="search notext" onclick="modalwindow = modal($('modal-{{$sejour->_id}}'));"></button>
	            <div id="modal-{{$sejour->_id}}" style="display: none; height: 600px; overflow: auto;">
	              <div style="float: right"> 
	              <button class="cancel" onclick="modalwindow.close();">Fermer</button>
	              </div>
	              {{assign var=sejour_id value=$sejour->_id}}
	              {{assign var=dossier_medical value=$sejour->_ref_patient->_ref_dossier_medical}}
	              {{if array_key_exists($sejour_id, $csteByTime)}}
	              {{assign var=csteByTime value=$csteByTime.$sejour_id}}
	              {{/if}}
	              {{mb_include module=dPurgences template=print_dossier}}
	            </div>
	          {{/if}}
						{{mb_value object=$sejour field="patient_id"}}
					</td>
        {{if $rpu->_id}}
					<td>
            {{if $rpu->ccmu}}
  						{{mb_value object=$rpu field="ccmu"}}
            {{/if}}
					</td>
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
          <td style="text-align: center">
            {{mb_ditto value=$sejour->_sortie|date_format:$dPconfig.date name=sortie}}
          </td>
					<td>{{$sejour->_sortie|date_format:$dPconfig.time}}</td>
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