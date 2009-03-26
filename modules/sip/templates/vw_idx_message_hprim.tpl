<script type="text/javascript">

sendMessage = function(message_hprim_id, message_hprim_classname, type_msg){
  var url = new Url;
  url.setModuleAction("sip", "httpreq_send_message");
  url.addParam("message_hprim_id", message_hprim_id);
  url.addParam("message_hprim_classname", message_hprim_classname);
	url.requestUpdate("systemMsg", { onComplete:function() { 
		 refreshMessage(message_hprim_id, message_hprim_classname, type_msg) }});
}

refreshMessage = function(message_hprim_id, message_hprim_classname, type_msg){
  var url = new Url;
  url.setModuleAction("sip", "httpreq_refresh_message");
  url.addParam("message_hprim_id", message_hprim_id);
  url.addParam("message_hprim_classname", message_hprim_classname);
  url.requestUpdate("msg_"+type_msg+"_"+message_hprim_id , { waitingText: null });
}

</script>

<table class="main">
  {{if !$msg_hprim->_id}}
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="9">MESSAGES HPRIM</th>
        </tr>
        <tr>
          <th></th>
          <th>{{mb_title object=$msg_hprim field="message_hprim_id"}}</th>
          <th>{{mb_title object=$msg_hprim field="date_production"}}</th>
          <th>{{mb_title object=$msg_hprim field="emetteur"}}</th>
          <th>{{mb_title object=$msg_hprim field="destinataire"}}</th>
          <th>{{mb_title object=$msg_hprim field="type"}}</th>
          <th>{{mb_title object=$msg_hprim field="sous_type"}}</th>
          <th>{{mb_title object=$msg_hprim field="date_echange"}}</th>
          <th>{{mb_title object=$msg_hprim field="acquittement"}}</th>
        </tr>
        {{foreach from=$listMessageHprim item=curr_msg_hprim}}
          <tbody id="msg_initiateur_{{$curr_msg_hprim->_id}}">
            {{include file="inc_message_hprim.tpl" object=$curr_msg_hprim}}
          </tbody>
          {{foreach from=$curr_msg_hprim->_ref_notifications item=curr_ref_notification}}
            <tbody id="msg_notification_{{$curr_msg_hprim->_id}}">
              {{include file="inc_message_hprim.tpl" object=$curr_ref_notification}}
            </tbody>
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{else}}
  <tr>
    <th class="title" style="text-transform: uppercase;">{{mb_title object=$msg_hprim field="message"}}</th>
    <th class="title" style="text-transform: uppercase;">{{mb_title object=$msg_hprim field="acquittement"}}</th>
  </tr>
  <tr>
    <td style="height:730px; width:50%">{{mb_value object=$msg_hprim field="message"}}</td>
    <td style="height:730px;">
      {{if $msg_hprim->acquittement}}
        {{mb_value object=$msg_hprim field="acquittement"}}
      {{else}}
        <div class="big-info">Aucun acquittement n'a été reçu.</div>
      {{/if}}
    </td>
  </tr>
  {{/if}}
</table>