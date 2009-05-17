{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $modules.dPsante400->_can->read}}
  <a href="#nowhere" onclick="guid_ids('{{$object->_guid}}')" title="{{tr}}CIdSante400-title-create{{/tr}}">
    <img src="images/icons/sante400.gif" alt="{{tr}}CIdSante400-title-create{{/tr}}"/>   
  </a>
{{/if}}
