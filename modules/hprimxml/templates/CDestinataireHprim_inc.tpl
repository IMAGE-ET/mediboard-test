{{* $Id: vw_idx_dest_hprim.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>  
  <th>{{mb_label object=$destinataire field="type"}}</th>
  <td>
    <input type="text" name="type" size="20" value="{{if $conf.sip.server}}cip{{else}}sip{{/if}}" readonly="readonly" />
  </td>
</tr>
<tr>
  <th>{{mb_label object=$destinataire field="register"}}</th>
  <td>{{mb_field object=$destinataire field="register"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$destinataire field="code_appli"}}</th>
  <td>{{mb_field object=$destinataire field="code_appli"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$destinataire field="code_acteur"}}</th>
  <td>{{mb_field object=$destinataire field="code_acteur"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$destinataire field="code_syst"}}</th>
  <td>{{mb_field object=$destinataire field="code_syst"}}</td>
</tr>