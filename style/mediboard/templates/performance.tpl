{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage mediboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul id="performance">
  <li class="performance-time" style="width: 10em;">
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
          <span class="performance-time">{{$dataSource.time|string_format:"%.3f"}} s</span>
        </li>
      {{/foreach}}
    </ul>
  </li>
  
  <li class="performance-memory">
    {{$performance.memoire}}
  </li>
  
  <li class="performance-objects" title="Objets chargés / cachables">
    <span class="performance-count">{{$performance.objets}}</span> / 
    <span class="performance-count">{{$performance.cachableCount}}</span>
    <ul>
      {{foreach from=$performance.objectCounts key=objectClass item=objectCount}}
        <li>
          <strong>{{$objectClass}}</strong>
          <span class="performance-count">{{$objectCount}}</span>
        </li>
      {{/foreach}}
      <li> <hr /> </li>
      {{foreach from=$performance.cachableCounts key=objectClass item=cachableCount}}
        <li>
          <strong>{{$objectClass}}</strong> 
          <span class="performance-count">{{$cachableCount}}</span>
        </li>
      {{/foreach}}
    </ul>
  </li>
  
  <li class="performance-autoload" title="Classes chargées / pas encore en cache">
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
    {{$performance.size}}
  </li>
  
  <li class="performance-network">
    {{$performance.ip}}
  </li>
  <li class="close" onclick="this.up('ul').remove()" title="{{tr}}Close{{/tr}}"></li>
</ul>
