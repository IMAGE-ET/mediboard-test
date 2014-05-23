{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{if $view_full_history && $total_aat > 0}}
  <tr>
    <td colspan="4">
      {{mb_include module=system template=inc_pagination total=$total_aat current=$page change_page="changePage" step=20}}
    </td>
  </tr>
{{/if}}
<tr>
  <th>{{tr}}CAvisArretTravail-motif_id{{/tr}}</th>
  <th>{{tr}}CAvisArretTravail-type{{/tr}}</th>
  <th>{{tr}}CAvisArretTravail-debut{{/tr}}</th>
  <th>{{tr}}CAvisArretTravail-fin{{/tr}}</th>
</tr>
{{foreach from=$aat_history item=_aat}}
  <tr>
    <td>{{mb_value object=$_aat field=libelle_motif}}</td>
    <td>{{mb_value object=$_aat field=type}}</td>
    <td>{{mb_value object=$_aat field=debut}}</td>
    <td>{{mb_value object=$_aat field=fin}}</td>
  </tr>
{{/foreach}}