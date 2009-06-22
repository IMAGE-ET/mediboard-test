{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if array_key_exists('dPsante400', $modules) && $modules.dPsante400->_can->read}}
  <a style="float: right;" href="#nowhere" title=""
  	onclick="guid_ids('{{$object->_guid}}')"  
  	onmouseover="ObjectTooltip.createEx(this,'{{$object->_guid}}', 'identifiers')">
    <img src="images/icons/sante400.gif" alt="{{tr}}CIdSante400-title-create{{/tr}}"/>   
  </a>
{{/if}}
	