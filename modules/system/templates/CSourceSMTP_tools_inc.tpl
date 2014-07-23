{{mb_script module=system script=smtp ajax=true}}

<!-- Test d'envoi SMTP -->
<button class="lookup notext compact" onclick="SMTP.connexion('{{$_source->name}}')">
  {{tr}}utilities-source-smtp-connexion{{/tr}}
</button>

<!-- Test d'envoi SMTP -->
<button class="send notext compact" onclick="SMTP.envoi('{{$_source->name}}');">
  {{tr}}utilities-source-smtp-envoi{{/tr}}
</button>