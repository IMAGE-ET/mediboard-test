{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage mediboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul id="performance">
  <li class="performance-time">
    <strong class="title">Temps de génération</strong>
    <span class="performance-time">{{$performance.genere}} s</span>
    
    {{assign var=dsTime value=0}}
    {{foreach from=$performance.dataSources key=dsn item=dataSource}}
      {{assign var=dsTime value=$dsTime+$dataSource.time}}
    {{/foreach}}
    {{math equation='(x/y)*100' assign=ratio x=$dsTime y=$performance.genere}}
    {{assign var=ratio value=$ratio|round:2}}
    
    <div class="performance-bar" title="{{$ratio}} % du temps passé en requêtes au SGBD"><div style="width: {{$ratio}}%;"></div></div>
    <ul>
      {{foreach from=$performance.dataSources key=dsn item=dataSource}}
        <li>
          <strong>{{$dsn}}</strong>
          <span class="performance-count">{{$dataSource.count}}</span> /
          <span class="performance-time">{{$dataSource.time*1000|string_format:"%.3f"}} ms</span>
          -
          <span class="performance-time">{{$dataSource.timeFetch*1000|string_format:"%.3f"}} ms</span>
        </li>
      {{/foreach}}
    </ul>
  </li>
  
  <li class="performance-memory">
    <strong class="title">Mémoire PHP</strong>
    {{$performance.memoire}}
  </li>
  
  <li class="performance-objects" title="Objets chargés / cachables">
    <strong class="title">Objets chargés / cachables</strong>
    <span class="performance-count">{{$performance.objets}}</span> / 
    <span class="performance-count">{{$performance.cachableCount}}</span>
    <ul>
      {{foreach from=$performance.objectCounts key=objectClass item=objectCount}}
        <li>
          <strong>{{$objectClass}}</strong>
          <span class="performance-count">{{$objectCount}}</span>
        </li>
      {{/foreach}}
      <li class="separator"> --- </li>
      {{foreach from=$performance.cachableCounts key=objectClass item=cachableCount}}
        <li>
          <strong>{{$objectClass}}</strong> 
          <span class="performance-count">{{$cachableCount}}</span>
        </li>
      {{/foreach}}
    </ul>
  </li>
  
  <li class="performance-autoload" title="Classes chargées / pas encore en cache">
    <strong class="title">Classes chargées / pas encore en cache</strong>
    <span class="performance-count">{{$performance.autoloadCount}}</span>
    <ul>
      {{foreach from=$performance.autoload key=objectClass item=time}}
        <li>
          <strong>{{$objectClass}}</strong>
          <span class="performance-time">{{$time|string_format:"%.3f"}} ms</span>
        </li>
      {{foreachelse}}
        <li class="empty">Aucune classe hors cache</li>
      {{/foreach}}
    </ul>
  </li>
  
  {{*
  <li class="performance-ccam">
    <span class="performance-count">{{$performance.ccam.cacheCount}}</span>
    <ul>
      <li>
        <strong>light</strong> 
        <span class="performance-count">{{$performance.ccam.useCount.1}}</span>
      </li>
      <li>
        <strong>medium</strong> 
        <span class="performance-count">{{$performance.ccam.useCount.2}}</span>
      </li>
      <li>
        <strong>full</strong> 
        <span class="performance-count">{{$performance.ccam.useCount.3}}</span>
      </li>
    </ul>
  </li>
  *}}

  <li class="performance-pagesize">
    <strong class="title">Taille de la page</strong>
    {{$performance.size}}
  </li>

  <li class="performance-l10n" id="i10n-alert" onclick="Localize.showForm()" title="{{tr}}system-msg-unlocalized_warning{{/tr}}">
    0
  </li>
  
  <li class="performance-network">
    <strong class="title">Adresse IP</strong>
    {{$performance.ip}}
  </li>
  <li class="export" onclick="window.open('data:text/html;charset=utf-8,'+encodeURIComponent(this.up('ul').innerHTML))" title="{{tr}}Export{{/tr}}"></li>
  <li class="close" onclick="this.up('ul').remove()" title="{{tr}}Close{{/tr}}"></li>
</ul>
