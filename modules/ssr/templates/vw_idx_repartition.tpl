{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=repartition}}

<table class="main">
  {{foreach from=$plateaux item=_plateau name=plateaux}}
  <tr>
    <td id="repartition-plateau-{{$_plateau->_id}}">
      <script type="text/javascript">Repartition.registerPlateau('{{$_plateau->_id}}')</script>
      <div class="small-info">{{$_plateau}}</div>
    </td>

    {{if $smarty.foreach.plateaux.first}}
    <td id="sejours_non_repartis" rowspan="100" style="width: 200px;">
      <script type="text/javascript">Repartition.registerSejours()</script>
      <div class="small-info">Séjours non répartis</div>
    </td>
    {{/if}}
  </tr>
  {{foreachelse}}
  <tr>
    <td><em>{{tr}}CGroups-back-plateaux_techniques.empty{{/tr}}</em></th>
  </tr>
  {{/foreach}}
  
</table>
