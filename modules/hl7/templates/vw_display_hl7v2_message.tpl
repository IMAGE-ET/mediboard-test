<script>
  highlightMessage = function(form) {
    return Url.update(form, "highlighted");
  }
  
  {{if $message}}
    Main.add(function(){
      highlightMessage(getForm("hl7v2-input-form"));
    });
  {{/if}}
</script>

<form name="hl7v2-input-form" action="?m=hl7&a=ajax_display_hl7v2_message" onsubmit="return highlightMessage(this)" method="post" class="prepared">
  <pre style="padding: 0; max-height: none;"><textarea name="message" rows="12" style="width: 100%; border: none; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; margin: 0; resize: vertical;">{{$message}}</textarea></pre>
  <button class="change">Valider</button>
</form>

<div id="highlighted"></div>
