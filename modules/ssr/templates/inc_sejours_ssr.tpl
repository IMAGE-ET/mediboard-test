{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table id="sejours-ssr" class="tbl">
	<tr>
		<th class="title" colspan="10">
			<span style="text-align: left">
				({{$sejours|@count}}) 
			</span>
			Séjours SSR du {{$date|date_format:$dPconfig.longdate}}
			
      {{if !$dialog}}
	    <form name="selDate" action="?" method="get">
	      <input type="hidden" name="m" value="{{$m}}" />
				<script type="text/javascript">
				Main.add(function () {
				  Calendar.regField(getForm("selDate").date, null, { noView: true } );
				});
				</script>
				
	      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
	    </form>
			{{/if}}
			
	  </th>
	</tr>
	<tr>
		{{assign var=url value="?m=$m&$actionType=$action&dialog=$dialog"}}
    <th style="width: 20em;">{{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url=$url}}</th>
    <th style="width: 1%;">
      <input type="text" size="6" onkeyup="SejoursSSR.filter(this)" id="filter-patient-name" />
    </th>
    <th style="width:  5em;">{{mb_colonne class="CSejour" field="entree"     order_col=$order_col order_way=$order_way url=$url}}</th>
    <th style="width:  5em;">{{mb_colonne class="CSejour" field="sortie"     order_col=$order_col order_way=$order_way url=$url}}</th>

		<th style="width:  5em;">
			DHE {{mb_title class=CSejour field=service_id}}

			{{if !$dialog}}
      <br />
      <select name="service_id" onchange="$V(getForm('Filter').service_id, $V(this), true);">
        <option value="">&mdash; {{tr}}All{{/tr}}</option>
        {{foreach from=$services item=_service}}
        <option value="{{$_service->_id}}" {{if $_service->_id == $filter->service_id}}selected="selected"{{/if}}>
          {{$_service}}
        </option>
        {{/foreach}}
      </select>
			{{/if}}
		</th>

    <th style="width: 20em;">{{mb_title class="CSejour" field="libelle"}}</th>

    <th style="width: 12em;">
      {{mb_title class="CSejour" field="praticien_id"}}
      {{if !$dialog}}
			<br />
			<select name="praticien_id" onchange="$V(getForm('Filter').praticien_id, $V(this), true);">
				<option value="">&mdash; {{tr}}All{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$praticiens selected=$filter->praticien_id}}
			</select>
      {{/if}}
    </th>

    <th style="width: 16em;">
		  {{mb_title class="CBilanSSR" field="_kine_referent_id"}} /
      {{mb_title class="CBilanSSR" field="_kine_journee_id"}}

      {{if !$dialog}}
      <br />
      <select name="referent_id" onchange="$V(getForm('Filter').referent_id, $V(this), true);">
        <option value="">&mdash; {{tr}}All{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$kines selected=$filter->referent_id}}
      </select>
      {{/if}}

		</th>
    
    <th style="width:   1%;" colspan="2"><label title="Evénements planifiés pour ce patient (ce jour - pendant tout le séjour)">Evt.</label></th>
	</tr>
	
	{{foreach from=$sejours key=sejour_id item=_sejour}}
	{{assign var=ssr_class value=""}}
	{{if !$_sejour->entree_reelle}}
	{{assign var=ssr_class value=ssr-prevu}}
	{{elseif $_sejour->sortie_reelle}}
	{{assign var=ssr_class value=ssr-termine}}
	{{/if}}

	<tr class="{{$ssr_class}}">
		<td colspan="2" class="text">
		  {{if $_sejour->_ref_prescription_sejour->_count_recent_modif_presc}}
			<img style="float: right" src="images/icons/ampoule.png" title="Prescription recemment modifiée"/>
			{{/if}}
		
			{{assign var=link value="?m=$m&tab=vw_aed_sejour_ssr&sejour_id=$sejour_id"}}
			{{mb_include template=inc_view_patient patient=$_sejour->_ref_patient}}
		</td>

	  {{assign var=distance_class value=ssr-far}}
	  {{if $_sejour->_entree_relative == "-1"}}
	  {{assign var=distance_class value=ssr-close}}
	  {{elseif $_sejour->_entree_relative == "0"}}
	  {{assign var=distance_class value=ssr-today}}
	  {{/if}}
    <td class="{{$distance_class}}">
    	{{mb_value object=$_sejour field=entree format=$dPconfig.date}}
      <div style="text-align: left; opacity: 0.6;">{{$_sejour->_entree_relative}}j</div>
		</td>

    {{assign var=distance_class value=ssr-far}}
    {{if $_sejour->_sortie_relative == "1"}}
    {{assign var=distance_class value=ssr-close}}
    {{elseif $_sejour->_sortie_relative == "0"}}
    {{assign var=distance_class value=ssr-today}}
    {{/if}}
    <td class="{{$distance_class}}">
    	{{mb_value object=$_sejour field=sortie format=$dPconfig.date}}
      <div style="text-align: right; opacity: 0.6;">{{$_sejour->_sortie_relative}}j</div>
		</td>
		
		<td style="text-align: center;">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
       {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$_sejour->_num_dossier}}
      </span>
			<div style="opacity: 0.6;">
       {{mb_value object=$_sejour field=service_id}}
			</div>
		</td>
		
    <td class="text">
      {{mb_include module=system template=inc_get_notes_image object=$_sejour mode=view float=right}}
    	{{mb_value object=$_sejour field=libelle}}
		</td>
		
		{{if $_sejour->annule}}
		<td colspan="4" class="cancelled">
			{{tr}}CSejour-{{$_sejour->recuse|ternary:"recuse":"annule"}}{{/tr}}
		</td>

		{{else}}
      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
      </td>
					
	    <td class="text">
	      {{assign var=bilan value=$_sejour->_ref_bilan_ssr}}
	      {{assign var=kine_referent value=$bilan->_ref_kine_referent}}
	      {{if $kine_referent->_id}}
	        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$kine_referent}}
	        {{assign var=kine_journee value=$bilan->_ref_kine_journee}}
	        {{if $kine_journee->_id != $kine_referent->_id}}
	        <br/>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$kine_journee}}
	        {{/if}}
	      {{/if}}
	    </td>
	    
	    {{assign var=prescription value=$_sejour->_ref_prescription_sejour}}
	    {{if !$prescription->_id}} 
	      <td colspan="2" style="text-align: center;">
	        <img src="images/icons/calendar-broken.png" title="Aucune prescription, planification impossible" />
	      </td>
	    {{else}}
	      <td style="text-align: right; width: 1%;">
	        {{if $_sejour->_count_evenements_ssr}} 
	        {{$_sejour->_count_evenements_ssr}}
	        {{/if}}
	      </td>
	  
	      <td style="text-align: right; width: 1%;">
	        {{if $_sejour->_count.evenements_ssr}} 
	        {{$_sejour->_count.evenements_ssr}}
	        {{/if}}
	      </td>
	    {{/if}}
		{{/if}}

	</tr>
	{{foreachelse}}
	<tr>
		<td colspan="10">
			<em>{{tr}}CSejour.none{{/tr}}</em>
		</td>
	</tr>
	{{/foreach}}
</table>