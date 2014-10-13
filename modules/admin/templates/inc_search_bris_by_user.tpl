{{*
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{foreach from=$bris item=_bris}}
  <tr>
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_bris->_ref_user}}</td>
    <td><span onmouseover="ObjectTooltip.createEx(this, '{{$_bris->_ref_object->_guid}}')">{{$_bris->_ref_object}}</span></td>
    <td class="text">{{mb_value object=$_bris field=comment}}</td>
    <td>{{mb_value object=$_bris field=date}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="4" class="empty">{{tr}}CBrisDeGlace.none{{/tr}}</td>
  </tr>
{{/foreach}}