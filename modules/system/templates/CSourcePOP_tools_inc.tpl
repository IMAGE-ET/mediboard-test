{{mb_script module=system script=pop ajax=true}}

<button class="lookup notext compact" onclick="POP.connexion('{{$_source->name}}','connexion')">Connexion</button>
<button class="search notext compact" onclick="POP.connexion('{{$_source->name}}', 'listBox')">Liste des boites filles</button>
{{if $can->admin}}
  <input type="checkbox" id="messagerie-auto" name="messagerie-auto" value="1"/>
  <button class="change notext compact" onclick="POP.getOldEmail('{{$_source->_id}}','{{$_source->name}}')">Récupérer les anciens mails</button>
{{/if}}