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
    <td class="narrow">
      <button type="button" class="tick" onclick="SenderFTP.readFilesSenders();">
        {{tr}}CSenderFTP-utilities_read-files-senders{{/tr}}
      </button> 
    </td>
    <td id="CSenderFTP-utilities_read-files-senders"></td>
  </tr> 
</table>
