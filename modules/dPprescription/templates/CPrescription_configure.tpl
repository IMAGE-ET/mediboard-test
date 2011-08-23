{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=class value=CPrescription}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

<table class="main">
  <tr>
    <td class="narrow">
    
<ul id="tab-{{$class}}" class="control_tabs_vertical" style="width: 20em;">
  <li>
    <a href="#general">Général</a>
    <a href="#chapters_visible">Chapitres visibles</a>
    <a href="#chapters_nursable">Chapitres prescriptibles par les exécutants</a>
    <a href="#scores">Scores</a>
   </li>
</ul>

<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tab-{{$class}}', true));
</script>

    </td><td>

	<table class="form">

    <tbody style="display: none" id="general">
			{{mb_include module=system template=inc_config_bool var=show_unsigned_lines}}
			{{mb_include module=system template=inc_config_bool var=show_unsigned_med_msg}}
			{{mb_include module=system template=inc_config_bool var=show_categories_plan_soins}}
			{{mb_include module=system template=inc_config_bool var=add_element_category}}
	    {{mb_include module=system template=inc_config_bool var=preselect_livret}}
			{{mb_include module=system template=inc_config_bool var=prescription_suivi_soins}}
	    {{mb_include module=system template=inc_config_bool var=use_libelle_livret}}
	    {{mb_include module=system template=inc_config_enum var=max_time_modif_suivi_soins values=$listHours skip_locales=1}}
      {{mb_include module=system template=inc_config_enum var=max_details_result values="10|20|50|100" skip_locales=1}}
			{{mb_include module=system template=inc_config_bool var=show_inscription}}
			{{mb_include module=system template=inc_config_bool var=manual_planif}}
			{{mb_include module=system template=inc_config_bool var=role_propre}}
      {{mb_include module=system template=inc_config_bool var=qte_obligatoire_inscription}}
		</tbody>

    <tbody style="display: none" id="chapters_visible">
	    {{mb_include module=system template=inc_config_bool var=show_chapter_med onchange="onchangeMed(this, 'show_chapter_med_elt')"}}
			{{mb_include module=system template=inc_config_bool var=show_chapter_med_elt onchange="onchangeMed(this, 'show_chapter_med')"}}
			
	    {{mb_include module=system template=inc_config_bool var=show_chapter_anapath}}
	    {{mb_include module=system template=inc_config_bool var=show_chapter_biologie}}
	    {{mb_include module=system template=inc_config_bool var=show_chapter_imagerie}}
	    {{mb_include module=system template=inc_config_bool var=show_chapter_consult}}
	    {{mb_include module=system template=inc_config_bool var=show_chapter_kine}}
	    {{mb_include module=system template=inc_config_bool var=show_chapter_soin}}
	    {{mb_include module=system template=inc_config_bool var=show_chapter_dm}}
	    {{mb_include module=system template=inc_config_bool var=show_chapter_dmi}}
			{{mb_include module=system template=inc_config_bool var=show_chapter_ds}}
		</tbody>
		
    <tbody style="display: none" id="chapters_nursable">
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_med}}
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_med_elt}}
	    
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_anapath}}
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_biologie}}
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_imagerie}}
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_consult}}
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_kine}}
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_soin}}
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_dm}}
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_dmi}}
			{{mb_include module=system template=inc_config_bool var=droits_infirmiers_ds}}
    </tbody>

	  <!-- Gestion des scores de prescription -->
    <tbody style="display: none" id="scores">

	  {{assign var="var" value="scores"}}
		<tr>
	    <th colspan="2" class="category">
	       {{tr}}config-{{$m}}-{{$class}}-{{$var}}-interaction{{/tr}}
	    </th>
	  </tr>
	  {{assign var=type_niveau value=interaction}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv1"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv2"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv3"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv4"}}
		<tr>
	    <th colspan="2" class="category">
	       {{tr}}config-{{$m}}-{{$class}}-{{$var}}-posologie{{/tr}}
	    </th>
	  </tr>
	  {{assign var=type_niveau value=posoqte}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv10"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv11"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv12"}}
	  {{assign var=type_niveau value=posoduree}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv20"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv21"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv22"}}
		<tr>
	    <th colspan="2" class="category">
	       {{tr}}config-{{$m}}-{{$class}}-{{$var}}-profil{{/tr}}
	    </th>
	  </tr>
	  {{assign var=type_niveau value=profil}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv0"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv1"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv2"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv9"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv30"}}
	  {{include file="inc_configure_niveau.tpl" niveau="niv39"}}
		<tr>
	    <th colspan="2" class="category">
	      Autres
	    </th>
	  </tr>
	  <tr>
	    <th style="width: 50%">
	      <label for="{{$m}}[{{$class}}][{{$var}}][IPC]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-IPC{{/tr}}">
	          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-IPC{{/tr}}
	      </label>  
	    </th>
		  <td>
		    <select name="{{$m}}[{{$class}}][{{$var}}][IPC]">
	        <option value="0" {{if 0 == $conf.$m.$class.$var.IPC}} selected="selected" {{/if}}>0</option>
	        <option value="1" {{if 1 == $conf.$m.$class.$var.IPC}} selected="selected" {{/if}}>1</option>
	        <option value="2" {{if 2 == $conf.$m.$class.$var.IPC}} selected="selected" {{/if}}>2</option>
	      </select>
		  </td>
		</tr>
		<tr>
		  <th>
	      <label for="{{$m}}[{{$class}}][{{$var}}][allergie]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-allergie{{/tr}}">
	          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-allergie{{/tr}}
	      </label>  
	    </th>
		  <td>
		    <select name="{{$m}}[{{$class}}][{{$var}}][allergie]">
	        <option value="0" {{if 0 == $conf.$m.$class.$var.allergie}} selected="selected" {{/if}}>0</option>
	        <option value="1" {{if 1 == $conf.$m.$class.$var.allergie}} selected="selected" {{/if}}>1</option>
	        <option value="2" {{if 2 == $conf.$m.$class.$var.allergie}} selected="selected" {{/if}}>2</option>
	      </select>
		  </td>
		</tr>
		<tr>
	    <th>
	      <label for="{{$m}}[{{$class}}][{{$var}}][hors_livret]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-hors_livret{{/tr}}">
	          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-hors_livret{{/tr}}
	      </label>  
	    </th>
		  <td>
		    <select name="{{$m}}[{{$class}}][{{$var}}][hors_livret]">
	        <option value="0" {{if 0 == $conf.$m.$class.$var.hors_livret}} selected="selected" {{/if}}>0</option>
	        <option value="1" {{if 1 == $conf.$m.$class.$var.hors_livret}} selected="selected" {{/if}}>1</option>
	        <option value="2" {{if 2 == $conf.$m.$class.$var.hors_livret}} selected="selected" {{/if}}>2</option>
	      </select>
		  </td>
		</tr>  

    </tbody>

	  <tr>
	    <td class="button" colspan="2">
	      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
	    </td>
	  </tr>
	</table>
	
    </td>
	</tr>
</table>	
	
</form>


