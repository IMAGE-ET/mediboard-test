{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6330 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<a style="float: right;" href="#nowhere" title=""
	onclick="guid_log('{{$object->_guid}}')"  
	onmouseover="ObjectTooltip.createEx(this,'{{$object->_guid}}', 'objectViewHistory')">
	<img src="images/icons/history.gif" width="16" height="16" />
</a>
