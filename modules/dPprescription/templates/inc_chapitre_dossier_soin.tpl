{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $chapitre == "med"}}
	{{foreach from=$prescription->_ref_lines_med_for_plan item=_cat_ATC key=_key_cat_ATC name="foreach_cat"}}
	  {{foreach from=$_cat_ATC item=_line name="foreach_med"}}
	    {{foreach from=$_line key=unite_prise item=line_med name="foreach_line"}} 
	      {{if !$line_med->_is_injectable}}
	      {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
		            line=$line_med
		            nodebug=true
		            first_foreach=foreach_med
		            last_foreach=foreach_line
		            global_foreach=foreach_cat
		            nb_line=$_line|@count
		            type="med"
		            dosql=do_prescription_line_medicament_aed}} 
	      {{/if}}
	   {{/foreach}}
	 {{/foreach}} 		 
	{{/foreach}}
{{elseif $chapitre == "inj"}}
  {{foreach from=$prescription->_ref_injections_for_plan item=inj_cat_ATC key=_key_cat_ATC name="_foreach_cat"}}
    {{foreach from=$inj_cat_ATC item=inj_line name="_foreach_med"}}
      {{foreach from=$inj_line key=unite_prise item=inj_line_med name="_foreach_line"}} 
        {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
            line=$inj_line_med
            nodebug=true
            first_foreach=_foreach_med
            last_foreach=_foreach_line
            global_foreach=_foreach_cat
            nb_line=$inj_line|@count
            type="inj"
            dosql=do_prescription_line_medicament_aed}}
     {{/foreach}}
   {{/foreach}} 		 
  {{/foreach}}
{{elseif $chapitre == "perfusion" || $chapitre == "oxygene" || $chapitre == "aerosol" || $chapitre == "alimentation"}}
	{{foreach from=$prescription->_ref_prescription_line_mixes_for_plan item=_prescription_line_mix name=foreach_prescription_line_mix}}
		<tr id="line_{{$_prescription_line_mix->_guid}}">
		  {{include file="../../dPprescription/templates/inc_vw_perf_dossier_soin.tpl" nodebug=true}}
		</tr>
	{{/foreach}}
{{elseif $chapitre == "inscription"}}

  {{foreach from=$prescription->_ref_inscriptions_for_plan item=_inscription name="foreach_inscription"}}
  	{{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl"
		  line=$_inscription
      nodebug=true
      first_foreach=""
      last_foreach=""
      global_foreach=""
			name_chap=""
      nb_line=1  
			type="med"
			unite_prise="aucune_prise"
      dosql=do_prescription_line_medicament_aed}}
	{{/foreach}}
  <tr> 
    <td class="text" colspan="2"><strong>Nouvelle inscription</strong></td>
	  <th class="before" style="cursor: pointer" onclick="showBefore();" onmouseout="clearTimeout(timeOutBefore);">
	   <img src="images/icons/a_left.png" />
	  </th>
    <td style="display: none;"></td>
		{{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
      {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
        {{foreach from=$_dates key=_date item=_hours}}
          {{foreach from=$_hours key=_heure_reelle item=_hour}} 
            <td class="{{$_view_date}}-{{$moment_journee}}">
              <div class="administration" ondblclick="addInscription('{{$_date}} {{$_hour}}:00:00', '{{$prescription->_id}}');"></div>
						</td>
					{{/foreach}}
				{{/foreach}}                           
      {{/foreach}}
    {{/foreach}}
    <td style="display: none;"></td>
    <th class="after" style="cursor: pointer" onclick="showAfter();" onmouseout="clearTimeout(timeOutAfter);">
      <img src="images/icons/a_right.png" />
    </th>
	  <td style="text-align: center"></td>
	  <td style="text-align: center"></td>
  </tr>
{{else}}
	{{assign var=elements value=$prescription->_ref_lines_elt_for_plan}}
	{{assign var=elements_chap value=$elements.$chapitre}}
	{{assign var=name_chap value=$chapitre}}
	  {{foreach from=$elements_chap key=name_cat item=elements_cat name="foreach_chap"}}
	    {{assign var=categorie value=$categories.$name_chap.$name_cat}}
	    {{foreach from=$elements_cat item=_element name="foreach_cat"}}
	      {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}} 	          
	        {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
	                  line=$element
	                  nodebug=true
	                  first_foreach=foreach_cat
	                  last_foreach=foreach_elt
	                  global_foreach=foreach_chap
	                  nb_line=$_element|@count
	                  dosql=do_prescription_line_element_aed}}
	      {{/foreach}}
	    {{/foreach}}
	  {{/foreach}}
{{/if}}