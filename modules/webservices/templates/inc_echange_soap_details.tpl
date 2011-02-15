{{* $Id: inc_echange_hprim.tpl 6287 2009-05-13 15:37:54Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-contenu', true);
  });
</script>

<table class="form">
  <tr>
    <th class="title">
      {{tr}}CEchangeSOAP{{/tr}} - {{$echange_soap->_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
      <br />
      - {{mb_value object=$echange_soap field="function_name"}} -
    </th>
  </tr>
  <tr>
    <td>
      <ul id="tabs-contenu" class="control_tabs">
        <li><a href="#input">{{mb_title object=$echange_soap field="input"}}</a></li>
        <li><a href="#output">{{mb_title object=$echange_soap field="output"}}</a></li>
        {{if $echange_soap->trace}}
        <li><a href="#lastRequestHeaders">{{mb_title object=$echange_soap field="last_request_headers"}}</a></li>
        <li><a href="#lastRequest">{{mb_title object=$echange_soap field="last_request"}}</a></li> 
        <li><a href="#lastResponseHeaders">{{mb_title object=$echange_soap field="last_response_headers"}}</a></li>
        <li><a href="#lastResponse">{{mb_title object=$echange_soap field="last_response"}}</a></li> 
        {{/if}}
      </ul>
      
      <hr class="control_tabs" />
    
      <div id="input" style="display: none;">
        {{mb_value object=$echange_soap field="input" export=true}}
      </div>
      
      <div id="output" style="display: none;">
        {{mb_value object=$echange_soap field="output" export=true}}
      </div>
      
      {{if $echange_soap->trace}}
        <div id="lastRequestHeaders" style="display: none;">
          {{mb_value object=$echange_soap field="last_request_headers"}}
        </div>
        <div id="lastRequest" style="display: none;">
          {{mb_value object=$echange_soap field="last_request"}}
        </div>
        <div id="lastResponseHeaders" style="display: none;">
          {{mb_value object=$echange_soap field="last_response_headers"}}
        </div>
        <div id="lastResponse" style="display: none;">
          {{mb_value object=$echange_soap field="last_response"}}
        </div>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td style="text-align: center;">
      <a target="blank" href="?m=webservices&a=download_echange&echange_soap_id={{$echange_soap->_id}}&dialog=1&suppressHeaders=1&message=1&acq=1" class="button modify">{{tr}}Download{{/tr}}</a>
    </td>
  </tr>
</table>
