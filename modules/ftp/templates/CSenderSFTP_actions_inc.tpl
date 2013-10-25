{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage ftp
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=ftp script=sender_ftp ajax=true}}

<table class="tbl">
  <tr>
    <th colspan="2" class="category">{{tr}}CSenderSFTP-utilities{{/tr}}</th>
  </tr>   
  <tr>
    <td class="narrow">
      <button type="button" class="tick" onclick="SenderFTP.dispatch('{{$actor->_guid}}');">
        {{tr}}CSenderFTP-utilities_dispatch{{/tr}}
      </button> 
    </td>
    <td id="CSenderFTP-utilities_dispatch"></td>
  </tr> 
</table>
