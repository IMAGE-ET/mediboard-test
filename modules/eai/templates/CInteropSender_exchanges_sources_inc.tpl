{{*
 * View Interop Receiver Exchange Sources EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<table class="form">  
  <tr>
    <th class="title">
      {{tr}}config-exchange-source{{/tr}}
    </th>
  </tr>
  <tr>
    <td> {{mb_include module=system template=inc_config_exchange_source source=$actor->_ref_exchanges_sources.0}} </td>
  </tr>
</table>