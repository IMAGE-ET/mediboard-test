</div>
    </td>
  </tr>
</table>

{{if $debugMode && !$offline}}
<div id="performance">
  PHP : 
  	{{$performance.genere}} sec. &ndash;
  	Poids de la page : {{$performance.size}} &ndash;
  	Mémoire {{$performance.memoire}}
  <br />
  
  Journal :
	  {{$performance.error}}   erreurs &ndash;
	  {{$performance.warning}} alertes &ndash;
	  {{$performance.notice}}  notices 
  <br />
  
  Objets métier : 
    {{$performance.objets}} chargements &ndash;
  	{{$performance.cachableCount}} cachable &ndash;
		{{$performance.autoload}} classes auto-chargées
  <br />
  
  Détails cachable :
  {{foreach from=$performance.cachableCounts key=objectClass item=cachableCount}}
  &ndash; {{$cachableCount}} {{$objectClass}}
	{{/foreach}}
  <br />

  Requêtes SQL : 
  {{foreach from=$performance.dataSources key=dsn item=dataSource}}
	  &ndash; {{$dataSource.count}} 
	  en {{$dataSource.time|string_format:"%.3f"}} sec.
	  sur '{{$dsn}}'
  {{/foreach}}
  <br />

  Utilisation CCAM : 
  	{{$performance.ccam.useCount.1}} light , 
  	{{$performance.ccam.useCount.2}} medium,
  	{{$performance.ccam.useCount.3}} full  &ndash;
  	{{$performance.ccam.cacheCount}} Appels au cache
  <br />
  Adresse IP : {{$userIP}}
</div>
{{/if}}

{{if !$offline}}
<div style="margin: 10px; float:right">
  <a href="http://www.sourceforge.net/projects/mediboard/" title="Projet Mediboard sur Sourceforge">
    <img src="http://www.sourceforge.net/sflogo.php?group_id=112072&amp;type=2" alt="Sourceforge Project Logo" />
  </a>
</div>
{{/if}}

</body>
</html>