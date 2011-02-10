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

<tr>
  <th>{{mb_label object=$actor field="message"}}</th>
  <td>{{mb_field object=$actor field="message"}}</td>
</tr>

{{mb_include module=$actor->_ref_module->mod_name template="`$actor->_class_name`_inc"}}