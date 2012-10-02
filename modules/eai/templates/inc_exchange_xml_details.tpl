{{*
 * Details Exchange XML EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if $exchange->_message === null && $exchange->_acquittement === null}}
  <div class="small-info">{{tr}}{{$exchange->_class}}-purge-desc{{/tr}}</div>
{{else}}
<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-contenu', true);
  });
</script>

<ul id="tabs-contenu" class="control_tabs">
  <li> 
    <a href="#message"> 
      {{mb_title object=$exchange field="_message"}} 
      <button class="modify notext" onclick="window.open('?m=eai&a=download_exchange&exchange_guid={{$exchange->_guid}}&dialog=1&suppressHeaders=1&message=1')"></button>
    </a> 
  </li>
  <li> 
    <a href="#ack" {{if !$exchange->_acquittement}}class="empty"{{/if}}>
      {{mb_title object=$exchange field="_acquittement"}}
      <button class="modify notext" onclick="window.open('?m=eai&a=download_exchange&exchange_guid={{$exchange->_guid}}&dialog=1&suppressHeaders=1&ack=1')"></button>
    </a> 
  </li>
</ul>

<hr class="control_tabs" />

<div id="message" style="display: none;">
  <div id="msg-message-view">
    {{mb_value object=$exchange field="_message" advanced=true}}
    <button type="button" class="edit" onclick="$('msg-message-view').toggle(); $('msg-message-edit').toggle();">{{tr}}Edit{{/tr}}</button>
  </div>
  
  <div id="msg-message-edit" style="display: none;">
    <form name="edit-xml-message" method="post" onsubmit="return onSubmitFormAjax(this, function(){ Control.Modal.close(); ExchangeDataFormat.viewExchange('{{$exchange->_guid}}'); })">
      <input type="hidden" name="m" value="eai" />
      <input type="hidden" name="dosql" value="do_exchange_content_edit" />
      <input type="hidden" name="exchange_guid" value="{{$exchange->_guid}}" />
      <textarea name="_message" rows="20" style="white-space: pre; word-wrap: normal; font-family: 'lucida console', 'courier new', courier, monospace; font-size: 10px; line-height: 1.3; overflow-x: auto; resize: vertical;">{{$exchange->_message}}</textarea>
      <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
      <button type="button" class="cancel" onclick="$('msg-message-view').toggle(); $('msg-message-edit').toggle();">{{tr}}Cancel{{/tr}}</button>
    </form>
  </div>
  
  {{if $exchange->message_valide != 1 && count($exchange->_doc_errors_msg) > 0}}
  <div class="big-error">
    <strong>{{tr}}CExchange-message-invalide{{/tr}}</strong> <br />
    <ul>
    {{foreach from=$exchange->_doc_errors_msg item=_error}}
      <li>{{$_error}}</li> 
     {{/foreach}} 
    </ul>
  </div>
  {{/if}}
</div>


<div id="ack" style="display: none;">
  {{if $exchange->message_valide == 1 || $exchange->acquittement_valide == 1}}
    {{if $exchange->_acquittement}}
      {{mb_value object=$exchange field="_acquittement" advanced=true}}
      
      {{mb_include module=$exchange->_ref_module->mod_name template="`$exchange->_class`_observations_inc"}}
    {{else}}
      <div class="small-info">{{tr}}CExchange-no-acquittement{{/tr}}</div>
    {{/if}}
  {{else}}
    {{if count($exchange->_doc_errors_ack) > 0}}
      <div class="big-error">
        <strong>{{tr}}CExchange-acquittement-invalide{{/tr}}</strong> <br />
        <ul>
        {{foreach from=$exchange->_doc_errors_ack item=_error}}
          <li>{{$_error}}</li> 
         {{/foreach}} 
        </ul>
      </div>
    {{else}}
      <div class="small-info">{{tr}}CExchange-no-acquittement{{/tr}}</div>
    {{/if}}
  {{/if}}
</div>
{{/if}}