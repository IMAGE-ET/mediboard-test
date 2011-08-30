{{* $Id: vw_placement_patients.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
{{mb_script module=dPhospi script=drag_patient}}
<table class="main">
	<tr>
	  <th class="title" style="width:200px;">Patiens  </th>
	  <th class="title">Service	  </th>
	</tr>
	<tr>
		<td>
			<div id="divGauche" style="min-width:100px;min-height:720px;clear:both;float:left;">
					<table class="main tbl">
						<tr><th>Non placés</th></tr>						
						  {{foreach from=$listAff key=_type_aff item=_liste_aff}}
						  {{foreach from=$_liste_aff item=_affectation}}
							  {{if $_type_aff != "Aff"}}
							  {{assign var=_sejour value=$_affectation}}
							  {{assign var=_patient   value=$_sejour->_ref_patient}}
							  <tr>
							    <td class="text">
									<div onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')" style="background-color: #EEE;" id="{{$_sejour->_id}}">
							       	 {{$_patient}}
									</div>
							    </td>
							  {{/if}}
							  </tr>
						  {{/foreach}}
						  {{/foreach}}
					</table>
			</div>
		</td>
		<td> 
			<div id="divDroite" style="float:left;margin-left:5px;margin-right:5px;">
				<form action="?m=dPhospi&tab=vw_placement_patients" method="post">
					<select name="service_id" onchange="this.form.submit()">
						<option value="">&mdash; Service</option>
						{{foreach from=$les_services item=_service}}		
							<option value="{{$_service->_id}}" {{if $service_id!="" && $service_id==$_service->_id}}selected="selected"{{/if}}>{{ $_service->nom}}</option>
						{{/foreach}}
					</select>
				</form>
				<table class="main tbl">
				{{foreach from=$zones item=_zone }}
					{{if $_zone==0}}<tr>{{elseif $_zone%10==0}}</tr><tr>{{/if}}
					{{if $les_chambres[$_zone]!="0"}}
					<td id="{{$les_chambres[$_zone]}}" style="white-space :normal;width:90px;height:90px;{{if $les_chambres[$_zone]!='null'}}background-color: #ABE;border-color: white;{{else}}background-color: white;border-color:white;{{/if}}
            {{if $_zone%10==0}}
              clear:both;
							{{/if}}
						float:left;">
						{{if $les_chambres[$_zone]!='null'}}{{$les_chambres[$_zone]}}		{{/if}}	
						{{foreach from=$listAff key=_type_aff item=_liste_aff}}
						{{foreach from=$_liste_aff item=_affectation}}
						  {{if $_type_aff =="Aff" && $_affectation->_ref_lit->_ref_chambre->nom==$les_chambres[$_zone]}}
							  {{assign var=_sejour value=$_affectation->_ref_sejour}}
							  {{assign var=_patient   value=$_sejour->_ref_patient}}
							  <div onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}');" style="background-color: #EEE;" id="{{$_sejour->_id}}">{{$_patient}}  </div>
						  {{/if}}
						{{/foreach}}
						{{/foreach}}
						</td> 
					{{/if}}
				{{/foreach}}
				</tr>
			</div>
		</td>
	</tr>
</table>