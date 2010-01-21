<script type="text/javascript">
function hideMessage(guid, setCookie) {
  var msgElement = $(guid);
  if (msgElement) {
    msgElement.hide();
    if (setCookie) new CookieJar().setValue("ClosedMessages", guid, true);
  }
}

Main.add(function () {
  $H(new CookieJar().get("ClosedMessages")).each(function(p){
    hideMessage(p.key, false);
  });
});
</script>

{{foreach from=$messages item=_message}}
<div id="{{$_message->_guid}}" class="{{if $_message->urgence == "urgent"}}error{{else}}message{{/if}}" 
     style="border-bottom: 1px solid #ccc; background-color: #f6f6f6; white-space: normal;">
  <a href="#1" style="float: right;" onclick="hideMessage('{{$_message->_guid}}', true);">{{tr}}Close{{/tr}}</a>
  <strong>{{$_message->titre}}</strong> : {{$_message->corps}}
</div>
{{/foreach}}