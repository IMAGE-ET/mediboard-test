{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=repartition}}

<form name="Edit-CBilanSSR" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="ssr" />
<input type="hidden" name="dosql" value="do_bilan_ssr_aed" />
<input type="hidden" name="del" value="0" />

{{mb_key object=$bilan}}
<br/>
{{mb_label object=$bilan field=sejour_id}}
{{mb_field object=$bilan field=sejour_id}}
<br/>
{{mb_label object=$bilan field=kine_id}}
{{mb_field object=$bilan field=kine_id}}

</form>

<table class="main">
  {{foreach from=$plateaux item=_plateau name=plateaux}}
  <tr>
    <td>
    	{{mb_include template=inc_repartition_plateau plateau=$_plateau}}
    </td>

    {{if $smarty.foreach.plateaux.first}}
    <td rowspan="100" style="width: 150px;">
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
