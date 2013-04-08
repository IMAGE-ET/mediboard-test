{{* $Id: inc_echange_hprim.tpl 6287 2009-05-13 15:37:54Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td>
   {{if $object->_self_sender}}
     <img src="images/icons/prev.png" alt="&lt;" />
   {{else}}
     <img src="images/icons/next.png" alt="&gt;" />
   {{/if}}
  </td>
  <td>
    <a target="blank" href="?m=webservices&a=download_echange&echange_soap_id={{$object->_id}}&dialog=1&suppressHeaders=1"
       class="button modify notext"></a>
  </td>
  <td class="narrow">
    <button type="button" onclick="viewEchange('{{$object->_id}}')" class="search">
     {{$object->_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
    </button>
  </td>
  <td>
    <span>
      <label title='{{mb_value object=$object field="date_echange"}}'>
        {{$object->date_echange}}
      </label>
    </span>
  </td>
  <td>
    {{if $object->_self_sender}}
     <label title='{{mb_value object=$object field="emetteur"}}' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       {{mb_value object=$object field="emetteur"}}
     {{/if}}
  </td>
  <td>
    {{if $object->_self_receiver}}
     <label title='{{mb_value object=$object field="destinataire"}}' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}');">
         {{mb_value object=$object field="destinataire"}}
       </span>
     {{/if}}
  </td>
  <td>{{mb_value object=$object field="type"}}</td>
  <td>{{mb_value object=$object field="web_service_name"}}</td>
  <td>{{mb_value object=$object field="function_name"}}</td>
  <td>{{if $object->input}}Oui{{else}}Non{{/if}}</td>
  <td {{if $object->soapfault}}class="error"{{/if}}>{{if $object->output}}Oui{{else}}Non{{/if}}</td>
  <td style="text-align: right;" 
      class="{{if $object->response_time > 10000}}error
      {{elseif $object->response_time > 1000}}warning
      {{elseif $object->response_time < 100}}ok{{/if}}"> 
    {{$object->response_time|round:0}} ms</td>
  <td {{if $object->trace}}class="warning"{{/if}}>{{mb_value object=$object field="trace"}}</td>
</tr>