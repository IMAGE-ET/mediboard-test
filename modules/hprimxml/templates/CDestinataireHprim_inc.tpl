{{* $Id: vw_idx_dest_hprim.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<tr>
  <th>{{mb_label object=$actor field="display_errors"}}</th>
  <td>{{mb_field object=$actor field="display_errors"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$actor field="register"}}</th>
  <td>{{mb_field object=$actor field="register"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$actor field="code_appli"}}</th>
  <td>{{mb_field object=$actor field="code_appli"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$actor field="code_acteur"}}</th>
  <td>{{mb_field object=$actor field="code_acteur"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$actor field="code_syst"}}</th>
  <td>{{mb_field object=$actor field="code_syst"}}</td>
</tr>