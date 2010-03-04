{{* $Id: vw_idx_dest_hprim.tpl 7814 2010-01-12 17:26:57Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7814 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !@$type}} 
  {{assign var="type" value=""}}
{{/if}}

{{if !$exchange_source_name}} 
  <div class="small-info">
    {{tr}}CExchangeSource-no-name{{/tr}}
  </div>
{{else}}
  <div id="exchange_source">
    <script type="text/javascript">
      refreshExchangeSource = function(exchange_source_name, type){
        var url = new Url("system", "ajax_refresh_exchange_source");
        url.addParam("type", type);
        url.addParam("exchange_source_name", exchange_source_name);
        url.requestUpdate('exchange_source');
      }
    </script>
    
    {{if !$type}} 
    <script type="text/javascript">
    Main.add(function () {
      Control.Tabs.create('tabs-exchange-source', true);
    });
    </script>
    
    <ul id="tabs-exchange-source" class="control_tabs">
      <li><a href="#ftp">{{tr}}CSourceFTP{{/tr}}</a></li>
      <li><a href="#soap">{{tr}}CSourceSOAP{{/tr}}</a></li>
    </ul>
      
    <hr class="control_tabs" />
    {{/if}} 
    
    <div id="ftp" style="display:{{if $type == "ftp"}}block{{else}}none{{/if}};">
      {{if isset($object->_id|smarty:nodefaults) && $object->_class_name != "CSourceFTP"}}
        <div class="small-info">
          {{tr}}CExchangeSource-already-exist{{/tr}}
        </div>
      {{else}}
      <table class="main">
        {{if !isset($object->_class_name|smarty:nodefaults)}}
          {{assign var="object" value=$exchange_objects.CSourceFTP}}
        {{/if}}
        {{if !isset($object->_id|smarty:nodefaults)}}
        <tr>
          <td class="halfPane">
            <a class="button new" onclick="$('config-source-ftp').show()">
              Créer une source FTP
            </a>
         </td>
        </tr>
        {{/if}}
        <tr>
          <td id="config-source-ftp" {{if !isset($object->_id|smarty:nodefaults)}}style="display:none"{{/if}}>
            {{mb_include module=system template=inc_config_source_ftp}}       
          </td>
        </tr>
      </table> 
      {{/if}}
    </div>
    
    <div id="soap" style="display:{{if $type == "soap"}}block{{else}}none{{/if}};">
      {{if isset($object->_id|smarty:nodefaults) && $object->_class_name != "CSourceSOAP"}}
        <div class="small-info">
          {{tr}}CExchangeSource-already-exist{{/tr}}
        </div>
      {{else}}
      <table class="main">
        {{if !isset($object->_class_name|smarty:nodefaults)}}
          {{assign var="object" value=$exchange_objects.CSourceSOAP}}
        {{/if}}
        {{if !isset($object->_id|smarty:nodefaults)}}
        <tr>
          <td class="halfPane">
            <a class="button new" onclick="$('config-source-soap').show()">
              Créer une source SOAP
            </a> 
         </td>
        </tr>
        {{/if}}
        <tr>
          <td id="config-source-soap" {{if !isset($object->_id|smarty:nodefaults)}}style="display:none"{{/if}}>
            {{mb_include module=system template=inc_config_source_soap}}        
          </td>
        </tr>
      </table>
      {{/if}}
    </div>
  </div>
{{/if}}