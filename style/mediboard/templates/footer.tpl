{{if $debugMode && !$offline}}
<div id="performance">
  PHP : {{$performance.genere}} secondes &ndash;
  Poids de la page : {{$performance.size}} &ndash;
  Mémoire {{$performance.memoire}}
  <br />
  Erreurs : {{$performance.error}} &ndash;
  Alertes : {{$performance.warning}} &ndash;
  Notices : {{$performance.notice}}
  <br />
  Objets métier : {{$performance.objets}} &ndash;
  Objets en cache : {{$performance.cache}} &ndash;
  Classes auto-chargées : {{$performance.autoload}}
  <br />
  Requêtes SQL : 
  {{foreach from=$dataSources item=ds}}
  {{if $ds}}
  &ndash; {{$ds->chrono->nbSteps}} 
  sur '{{$ds->dsn}}'
  en {{$ds->chrono->total|string_format:"%.3f"}} secondes
  {{/if}}
  {{/foreach}}
  <br />
  Adresse IP : {{$userIP}}
</div>
{{/if}}

    </td>
  </tr>
</table>


{{if $demoVersion && !$offline}}
<div style="margin: 10px; float:right">
  <a href="http://www.sourceforge.net/projects/mediboard/" title="Projet Mediboard sur Sourceforge">
    <img src="http://www.sourceforge.net/sflogo.php?group_id=112072&amp;type=2" alt="Sourceforge Project Logo" />
  </a>
</div>
{{/if}}

</body>
</html>