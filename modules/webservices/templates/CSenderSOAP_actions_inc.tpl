{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=webservices script=sender_soap ajax=true}}

<form name="formExecute" method="post" action="?m=webservices">
  <table class="tbl">
    <tr>
      <th colspan="2" class="category">{{tr}}CSenderSOAP-utilities{{/tr}}</th>
    </tr>   
    <tr>
      <td colspan="2">
        <textarea id="message" rows="10"></textarea>
      </td>
    </tr>
    <tr>
      <td class="narrow">
        <button type="button" class="tick" onclick="SenderSOAP.dispatch('{{$actor->_id}}', $V(this.form.message));">
          {{tr}}CSenderSOAP-utilities_dispatch{{/tr}}
        </button> 
      </td>
      <td id="CSenderSOAP-utilities_dispatch"></td>
    </tr> 
  </table>
</form>
