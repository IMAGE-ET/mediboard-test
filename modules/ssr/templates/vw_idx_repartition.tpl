{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

showLegende = function(){
  var url = new Url();
	url.setModuleAction("ssr", "vw_legende");
	url.popup(300, 200, "Legende");
}


Main.add(function () {
  Calendar.regField(getForm("selDate").date, null, {noView: true});
});

</script>

{{mb_include_script module=ssr script=repartition}}

<form name="Edit-CBilanSSR" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="ssr" />
<input type="hidden" name="dosql" value="do_bilan_ssr_aed" />
<input type="hidden" name="del" value="0" />

{{mb_key object=$bilan}}
{{mb_field object=$bilan field=sejour_id hidden=1}}
{{mb_field object=$bilan field=kine_id hidden=1}}

</form>

<table class="main">
  <tr>
	<th class="title" colspan="2">Planning du {{$date|date_format:$dPconfig.date}}
		<form name="selDate" action="?" method="get">
			<input type="hidden" name="m" value="{{$m}}" />
		  <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
		</form>
		<button type="button" class="search" style="float: right" onclick="showLegende();">Legende</button>
	</th>
	</tr>
  {{foreach from=$plateaux item=_plateau name=plateaux}}
  <tr>
    <td>
    	{{mb_include template=inc_repartition_plateau plateau=$_plateau}}
    </td>

    {{if $smarty.foreach.plateaux.first}}
    <td rowspan="100" style="width: 180px;">
      {{mb_include template=inc_sejours_non_affectes}}
    </td>
    {{/if}}
  </tr>
  {{foreachelse}}
  <tr>
    <td><em>{{tr}}CGroups-back-plateaux_techniques.empty{{/tr}}</em></th>
  </tr>
  {{/foreach}}
  
</table>
