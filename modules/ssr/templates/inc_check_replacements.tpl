{{* $Id: configure.tpl 7993 2010-02-03 16:55:27Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7993 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<table class="tbl">
  <tr>
    <th class="title" colspan="1">{{tr}}CSejour{{/tr}}</th>
    <th class="title" colspan="2">{{tr}}CPlageConge{{/tr}}</th>
    <th class="title" colspan="4">{{tr}}CReplacement{{/tr}} <small>({{$replacements|@count}})</small></th>
  </tr>

  <tr>
    <th>{{mb_title class=CSejour field=patient_id}} </th>
    <th>{{mb_title class=CPlageConge field=user_id}}</th>
    <th>{{mb_title class=CPlageConge field=libelle}}</th>
    <th>{{mb_title class=CReplacement field=_min_deb}} / {{mb_title class=CReplacement field=_max_fin}}</th>
    <th>{{mb_title class=CReplacement field=replacer_id}}</th>
    <th colspan="2">Conges en conflits</th>
		
  </tr>
	
{{foreach from=$replacements item=_replacement}}
  <tr>
    <td>
    	{{assign var=sejour value=$_replacement->_ref_sejour}}
    	<span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
    		{{mb_value object=$sejour->_ref_patient field=nom}}
    	</span>
		</td>
    {{assign var=conge value=$_replacement->_ref_conge}}
    <td>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$conge->_ref_user}}
		</td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$conge->_guid}}')">
    		{{mb_value object=$conge field=libelle}}
			</span>
		</td>
    <td>{{mb_include module=system template=inc_interval_date from=$_replacement->_min_deb to=$_replacement->_max_fin}}</td>
    <td>
    	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_replacement->_ref_replacer}}
		<td>
			{{foreach from=$_replacement->_ref_replacer_conges item=_replacer_conge}}
			<div>
				<strong>
					{{mb_include module=system template=inc_opened_interval_date from=$_replacer_conge->date_debut to=$_replacer_conge->date_fin}}
				</strong>
			</div>
			{{/foreach}}
			
			<div style="padding-left: 1em;">
	      {{foreach from=$_replacement->_ref_replacement_fragments item=_fragment}}
	      <div>
	        {{mb_include module=system template=inc_opened_interval_date from=$_fragment.0 to=$_fragment.1}}
	      </div>
	      {{/foreach}}
      </div>
		</td>
		<td>
      <button type="button" class="change" style="float: right;"></button>
		</td>
  </tr>
   
{{foreachelse}}
{{/foreach}}
  
</table>