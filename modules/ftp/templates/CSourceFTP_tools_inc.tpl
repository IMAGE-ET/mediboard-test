{{mb_script module=ftp script=action_ftp ajax=true}}

<!-- Test connexion FTP -->
<button class="search notext compact" onclick="FTP.connexion('{{$_source->name}}')">
  {{tr}}utilities-source-ftp-connexion{{/tr}}
</button>

<!-- Liste des fichiers -->
<button class="list notext compact" onclick="FTP.getFiles('{{$_source->name}}')">
  {{tr}}utilities-source-ftp-getFiles{{/tr}}
</button>