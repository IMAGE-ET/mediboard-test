{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function updateDays() {
  var target = getForm("editConfig")['{{$m}}[dispensation_schedule]'];
  target.value = [$V(getForm("editConfigView").days)].flatten().join('');
}
</script>

<table class="form">
  <col style="width: 50%" />

  {{assign var="var" value="dispensation_schedule"}}
  <tr>
    <th>{{tr}}config-{{$m}}-{{$var}}{{/tr}}</th>
    <td>
      <form name="editConfigView" action="" method="post" onsubmit="return false">
      {{foreach from=$list_days item=day key=key}}
        <label><input type="checkbox" value="{{$key}}" name="days" {{if $list_days_schedule.$key}}checked="checked"{{/if}} onclick="updateDays()" /> {{$day}}</label><br />
      {{/foreach}}
      </form>
    </td>
  </tr>
</table>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  {{assign var="var" value="dispensation_schedule"}}
  <input type="hidden" name="{{$m}}[{{$var}}]" value="{{$conf.$m.$var}}" />
  
  <table class="form">
    <col style="width: 50%" />
    
    {{mb_include module=system template=inc_config_bool var=show_totals_in_lists}}
    {{mb_include module=system template=inc_config_str var=num_days_date_min numeric=true size=3}}
  
	  <tr>
	  	<th colspan="2" class="category">
	  		Pilulier
	  	</th>
		</tr>
		
    {{section name=periodes start=1 loop=6}}
	    {{assign var=num_period value=$smarty.section.periodes.index}}
		  {{assign var="class" value="periode_$num_period"}}
		  {{mb_include module=system template=inc_config_enum var=heure values=$list_hours skip_locales=1 defaultValue="aaa"}}
			{{mb_include module=system template=inc_config_str var=libelle}}
			{{mb_include module=system template=inc_config_color var=couleur}}
    {{/section}}
	
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
