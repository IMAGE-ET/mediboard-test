{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if array_key_exists('dPsante400', $modules) && $modules.dPsante400->_can->read}}
  <a style="float: right;" href="#1" title=""
  	onclick="guid_ids('{{$object->_guid}}')"  
  	onmouseover="ObjectTooltip.createEx(this,'{{$object->_guid}}', 'identifiers')">
    <img src="images/icons/external.png" width="16" height="16" />   
  </a>
{{/if}}
	