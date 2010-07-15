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
   {{if $object->_self_emetteur}}
     <img src="images/icons/prev.png" alt="&lt;" />
   {{else}}
     <img src="images/icons/next.png" alt="&gt;" />
   {{/if}}
  </td>
  <td>
    <a target="blank" href="?m=webservices&a=download_echange&echange_soap_id={{$curr_echange_soap->_id}}&dialog=1&suppressHeaders=1" class="button modify notext"></a>
  </td>
  <td>
    <a href="?m=webservices&amp;tab=vw_idx_echange_soap&amp;echange_soap_id={{$object->_id}}" class="button search">
     {{$object->echange_soap_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
    </a>
  </td>
  <td>
    <span>
      <label title='{{mb_value object=$object field="date_echange"}}'>
        {{mb_value object=$object field="date_echange"}}
      </label>
    </span>
  </td>
  <td>
    {{if $object->_self_emetteur}}
     <label title='{{mb_value object=$object field="emetteur"}}' style="font-weight:bold">
       [SELF]
     </label>
     {{else}}
       {{mb_value object=$object field="emetteur"}}
     {{/if}}
  </td>
  <td>
    {{if $object->_self_destinataire}}
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
</tr>