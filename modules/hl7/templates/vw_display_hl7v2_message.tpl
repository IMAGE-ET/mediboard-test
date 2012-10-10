<script>
  highlightER7 = function(form) {
    var url = new Url("hl7", "ajax_display_hl7v2_message");
    url.addElement(form.message);
    url.requestUpdate("highlighted");
    return false;
  }
  
  {{if $message}}
    Main.add(function(){
      highlightER7(getForm("hl7v2-input-form"));
    });
  {{/if}}
</script>

<form name="hl7v2-input-form" action="?" onsubmit="return highlightER7(this)" method="get" class="prepared">
  <pre style="padding: 0; max-height: none;"><textarea name="message" rows="12" style="width: 100%; border: none; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; margin: 0; resize: vertical;">{{$message}}</textarea></pre>
  <button class="change">Valider</button>
</form>

<div id="highlighted"></div>
