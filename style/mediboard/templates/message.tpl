<script type="text/javascript">
function hideMessage(guid, setCookie) {
  $(guid).hide();
  if(setCookie)
      new CookieJar().setValue("ClosedMessages", guid, true);
}

Main.add(function () {
  $H(new CookieJar().get("ClosedMessages")).each(function(p){
      hideMessage(p.key, false);
    });
});
</script>


{{foreach from=$messages item=_message}}
<div id="{{$_message->_guid}}" class="{{if $_message->urgence == "urgent"}}error{{else}}info{{/if}}" style="border-bottom: 1px solid #999; background-color: #eee;">
  <a href="#1" style="float: right;" onclick="hideMessage('{{$_message->_guid}}', true);">Fermer</a>
  <strong>{{$_message->titre}}</strong> : {{$_message->corps}}
</div>
{{/foreach}}