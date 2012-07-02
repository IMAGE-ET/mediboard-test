{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=eai    script=exchange_data_format ajax=true}}
{{mb_script module=system script=sender_fs            ajax=true}}

<table class="main">
  <tr>
    <th colspan="2" class="category">{{tr}}CSenderFileSystem-utilities{{/tr}}</th>
  </tr>  
  
  <tr>
    <td class="narrow">
      <button type="button" class="tick" onclick="SenderFS.createExchanges('{{$actor->_guid}}');">
        {{tr}}CSenderFileSystem-utilities_create_exchanges{{/tr}}
      </button> 
    </td>
    <td id="CSenderFileSystem-create_exchanges"></td>
  </tr> 
  
  <tr>
    <td class="narrow">
      <button type="button" class="tick" onclick="ExchangeDataFormat.treatmentExchanges('{{$actor->_guid}}');">
        {{tr}}CExchangeDataFormat-utilities_treatment_exchanges{{/tr}}
      </button> 
    </td>
    <td id="CExchangeDataFormat-treatment_exchanges"></td>
  </tr> 
   
  <tr>
    <td class="narrow">
      <button type="button" class="tick" onclick="SenderFS.dispatch('{{$actor->_guid}}');">
        {{tr}}CSenderFileSystem-utilities_dispatch{{/tr}}
      </button> 
    </td>
    <td id="CSenderFileSystem-utilities_dispatch"></td>
  </tr> 
</table>
