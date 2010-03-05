{{* $Id: vw_idx_dest_hprim.tpl 7814 2010-01-12 17:26:57Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7814 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="sourcename" value=$source->name}}

{{if !$sourcename}} 
  <div class="small-info">
    {{tr}}CExchangeSource-no-name{{/tr}}
  </div>
{{else}}
  <div id="exchange_source-{{$sourcename}}">
    <script type="text/javascript">
      refreshExchangeSource = function(exchange_source_name, type){
        var url = new Url("system", "ajax_refresh_exchange_source");
        url.addParam("exchange_source_name", exchange_source_name);
        url.addParam("type", type);
        url.requestUpdate('exchange_source-'+exchange_source_name);
      }
    </script>
    
    {{if $source->_allowed_instances}} 
    <script type="text/javascript">
    Main.add(function () {
      Control.Tabs.create('tabs-exchange-source-{{$sourcename}}', true);
    });
    </script>
    
    <ul id="tabs-exchange-source-{{$sourcename}}" class="control_tabs">
      {{foreach from=$source->_allowed_instances item=_source_allowed}}
      <li><a href="#{{$_source_allowed->_class_name}}-{{$sourcename}}">{{tr}}{{$_source_allowed->_class_name}}{{/tr}}</a></li>
     {{/foreach}}
    </ul>
      
    <hr class="control_tabs" />
    {{/if}} 
    
    <div id="CSourceFTP-{{$sourcename}}" style="display:{{if !$source->_allowed_instances && ($source instanceof CSourceFTP)}}block{{else}}none{{/if}};">
      {{if $source->_id && !($source instanceof CSourceFTP)}}
        <div class="small-info">
          {{tr}}CExchangeSource-already-exist{{/tr}}
        </div>
      {{else}}
      <table class="main">
        {{assign var="_source_ftp" value=$source}}
        {{if $source->_class_name == "CExchangeSource"}}
          {{assign var="_source_ftp" value=$source->_allowed_instances.CSourceFTP}}
        {{/if}}
        {{if !$source->_id}}
        <tr>
          <td class="halfPane">
            <a class="button new" onclick="$('config-source-ftp-{{$sourcename}}').show()">
              Créer une source FTP
            </a>
         </td>
        </tr>
        {{/if}}
        <tr>
          <td id="config-source-ftp-{{$sourcename}}" {{if !$source->_id}}style="display:none"{{/if}}>
            {{mb_include module=system template=inc_config_source_ftp source=$_source_ftp}}       
          </td>
        </tr>
      </table> 
      {{/if}}
    </div>
    
    <div id="CSourceSOAP-{{$sourcename}}" style="display:{{if !$source->_allowed_instances && ($source instanceof CSourceSOAP)}}block{{else}}none{{/if}};">
      {{if $source->_id && !($source instanceof CSourceSOAP)}}
        <div class="small-info">
          {{tr}}CExchangeSource-already-exist{{/tr}}
        </div>
      {{else}}
      <table class="main">
        {{assign var="_source_soap" value=$source}}
        {{if $source->_class_name == "CExchangeSource"}}
          {{assign var="_source_soap" value=$source->_allowed_instances.CSourceSOAP}}
        {{/if}}
        {{if !$source->_id}}
        <tr>
          <td class="halfPane">
            <a class="button new" onclick="$('config-source-soap-{{$sourcename}}').show()">
              Créer une source SOAP
            </a> 
         </td>
        </tr>
        {{/if}}
        <tr>
          <td id="config-source-soap-{{$sourcename}}" {{if !$source->_id}}style="display:none"{{/if}}>
            {{mb_include module=system template=inc_config_source_soap source=$_source_soap}}        
          </td>
        </tr>
      </table>
      {{/if}}
    </div>
  </div>
{{/if}}