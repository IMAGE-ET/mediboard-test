{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main" style="font-size: 1.1em;">
  <tr>
    <th>
    	{{if $offline}}
	      <span style="float: right">
	        {{$dateTime|date_format:$dPconfig.datetime}}
	      </span>
	    {{/if}}
       <a href="#print" onclick="window.print()">
        Main courante du 
				{{$date|date_format:$dPconfig.longdate}}
				<br /> Total: {{$sejours|@count}} RPU
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th style="width:   1%;" colspan="2">{{mb_title class=CRPU field=_entree}}</th>
				  <th style="width: 16em;">{{mb_label class=CRPU field=_patient_id}}</th>
				  <th style="width:  8em;">{{mb_label class=CRPU field=ccmu}}</th>
					<th style="width: 16em;">{{mb_label class=CRPU field=diag_infirmier}}</th>
					<th style="width:   1%;">Heure PeC</th>
				  <th style="width:  8em;">{{mb_label class=CRPU field=_responsable_id}}</th>  
					<th style="width:   1%;">
						{{mb_label class=CSejour field=mode_sortie}} 
						<br/> &amp; 
						{{mb_label class=CRPU field=orientation}}
					</th>
				  <th style="width:   1%;" colspan="2">{{mb_title class=CRPU field=_sortie}}</th>
				</tr>
			  {{foreach from=$sejours item=sejour}}
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
	              {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
	              {{if array_key_exists($sejour_id, $csteByTime)}}
	              {{assign var=csteByTime value=$csteByTime.$sejour_id}}
	              {{/if}}
	              {{mb_include module=dPurgences template=print_dossier}}
	            </div>
	          {{/if}}
						<big>{{$patient}}</big>
						{{if $dPconfig.dPurgences.age_patient_rpu_view}}
						  <br/>{{$patient->_age}} ans
						{{/if}}
					</td>
        {{if $rpu->_id}}
					<td class="ccmu-{{$rpu->ccmu}} text">
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
						  &gt; <strong>{{mb_value object=$sejour field="etablissement_transfert_id"}}</strong>
						{{/if}}
            {{if $sejour->mode_sortie == "mutation"}}
              <br />
              &gt; <strong>{{mb_value object=$sejour field="service_mutation_id"}}</strong>
            {{/if}}
						{{if $rpu->orientation}}
							<br />
						  {{mb_value object=$rpu field="orientation"}}
						{{/if}}
					</td>
          
					
          {{if $sejour->type != "urg"}}
            <td colspan="2" class="text arretee">
    					<strong>{{mb_value object=$sejour field=type}}</strong>
						</td>
					{{else}}
						{{if !$sejour->sortie_reelle}}
	  					<td colspan="2" />
						{{else}}
		          <td style="text-align: center">
		            {{mb_ditto value=$sejour->sortie_reelle|date_format:$dPconfig.date name=sortie}}
		          </td>
							<td>{{$sejour->_sortie|date_format:$dPconfig.time}}</td>
	          {{/if}}
					{{/if}}
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

<table class="tbl">
	<tr>
		<th class="title" colspan="10">Statistiques</th>
	</tr>
	<tr>
		<th>{{mb_title class=CPatient field=_age}}</th>
    <th>
    	{{mb_title class=CSejour field=etablissement_transfert_id}}
			<small>({{$stats.transferts_count}})</small>
		</th>
    <th>
    	{{mb_title class=CSejour field=service_mutation_id}}
      <small>({{$stats.mutations_count}})</small>
		</th>
	</tr>
	<tr>
		<td>
			<ul>
			  <li>
			    Patients de moins de 1 ans : 
			    <strong>{{$stats.less_than_1}}</strong>
			  </li>
			  <li>
			    Patients de 75 ans ou plus : 
			    <strong>{{$stats.more_than_75}}</strong>
			  </li>
			</ul>
		</td>

    <td>
      <ul>
      	{{foreach from=$stats.etablissements_transfert item=_etablissement_transfert}}
      	<li>
      		{{$_etablissement_transfert.ref}} : 
					<strong>{{$_etablissement_transfert.count}}</strong>
				</li>
      	{{foreachelse}}
        <li>
          <em>{{tr}}None{{/tr}}</em>
        </li>
      	{{/foreach}}
      </ul>
    </td>

    <td>
      <ul>
        {{foreach from=$stats.services_mutation item=_service_mutation}}
        <li>
          {{$_service_mutation.ref}} : 
          <strong>{{$_service_mutation.count}}</strong>
        </li>
        {{foreachelse}}
        <li>
          <em>{{tr}}None{{/tr}}</em>
        </li>
        {{/foreach}}
      </ul>
    </td>
	</tr>
</table>
