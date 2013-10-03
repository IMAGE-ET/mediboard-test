{{*
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=eai script=CTunnel ajax=true}}

<button class="new" onclick="CTunnel.editTunnel('0')">{{tr}}CHTTPTunnelObject-title-create{{/tr}}</button>
<div id="listTunnel">
  {{mb_include template=inc_list_tunnel}}
</div>
<br/>
<div id="result_action">
</div>