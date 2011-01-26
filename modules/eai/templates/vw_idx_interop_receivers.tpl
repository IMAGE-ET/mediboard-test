{{*
 * View Interop Receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{mb_include_script module=eai script=interop_receiver}}

<table class="main">
  <tr>
    <td style="width:30%" rowspan="6" id="receivers">
      {{mb_include template=inc_receivers}}
    </td>
    <td class="halfPane" id="receiver">
      {{mb_include template=inc_receiver}}
    </td> 
  </tr>
</table>