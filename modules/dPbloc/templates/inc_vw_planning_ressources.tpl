{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  createUsage = function(ressource_materielle_id) {
    var form = getForm("createUsageForm");
    $V(form.ressource_materielle_id, ressource_materielle_id);
    onSubmitFormAjax(form, function() {
      window.parent.Control.Modal.close();
      window.parent.reloadModal();
    });
  }
  
  Main.add(function() {
    var form = getForm("filterDate");
    Calendar.regField(form.date, null, {noView: true});
  });
</script>

{{*
<form name="filterPlanning" method="get">
  <input type="hidden" name="m" value="bloc" />
  <input type="hidden" name="a" value="ajax_vw_planning_ressources" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="operation_id" value="{{$operation->_id}}" />
  <input type="hidden" name="besoin_ressource_id" value="{{$besoin_ressource_id}}" />
  <select name="type_ressource_id" onchange="this.form.submit()">
    {{foreach from=$types_ressources item=_type_ressource}}
      <option value="{{$_type_ressource->_id}}" {{if $_type_ressource->_id == $type_ressource_id}}selected{{/if}}>
        {{$_type_ressource->libelle}}
      </option>
    {{/foreach}}
  </select>
</form>
 *}}

{{* Légende du planning *}}
<div id="legend" style="display: none">
  <table class="tbl">
    <tr>
      <th colspan="3">
        Légende
      </th>
    </tr>
    <tr>
      <td class="button">
        <div class="planning_ressource interv" style="width: 100%; height: 1.5em;margin: 0 !important;">Intervention</div>
      </td>
    </tr>
    <tr>
      <td class="button">
        <div class="planning_ressource usage" style="width: 100%; margin: 0 !important;">Ressource assignée</div>
      </td>
    </tr>
    <tr>
      <td class="button">
        <div class="planning_ressource usage_selected" style="width: 100%; margin: 0 !important;">Ressource assignée à l'intervention courante</div>
      </td>
    </tr>
    <tr>
      <td class="button">
        <div class="planning_ressource indispo" style="width: 100%; margin: 0 !important;">Ressource indisponible</div>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="3">
        <button type="button" class="close" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</div>

<form name="createUsageForm" method="post">
  <input type="hidden" name="m" value="dPbloc" />
  <input type="hidden" name="dosql" value="do_usage_ressource_aed" />
  <input type="hidden" name="usage_ressource_id" value="" />
  <input type="hidden" name="besoin_ressource_id" value="{{$besoin_ressource_id}}" />
  <input type="hidden" name="ressource_materielle_id" value="" />
</form>

{{math equation=x*y x=$ressources|@count y=25.7 assign=height}}
{{if $smarty.session.browser.name == "firefox" || $smarty.session.browser.name == "msie"}}
  {{math equation=x/y x=90 y=$hours|@count assign=td_width}}
{{else}}
  {{math equation=x/y x=89 y=$hours|@count assign=td_width}}
{{/if}}

<table class="tbl" style="table-layout: fixed;">
  <col style="width: 10%" />
  <tr>
    <th colspan="{{math equation=x+1 x=$hours|@count}}" class="title">
      <button type="button" class="search" style="float: right;" onclick="modal('legend')">Légende</button>
      <span>
      <a href="?m=bloc&a=ajax_vw_planning_ressources&operation_id={{$operation->_id}}&type_ressource_id={{$type_ressource_id}}&besoin_ressource_id={{$besoin_ressource_id}}&date={{$date_before}}&dialog=1" style="display: inline;">&lt;&lt;&lt;</a>
      {{$date|date_format:$conf.longdate}}
      <form name="filterDate" method="get">
        <input type="hidden" name="m" value="dPbloc" />
        <input type="hidden" name="a" value="ajax_vw_planning_ressources" />
        <input type="hidden" name="dialog" value="1" />
        <input type="hidden" name="operation_id" value="{{$operation->_id}}" />
        <input type="hidden" name="type_ressource_id" value="{{$type_ressource_id}}" />
        <input type="hidden" name="besoin_ressource_id" value="{{$besoin_ressource_id}}" />
        <input type="hidden" name="date" class="date notNull" value="{{$date}}" onchange="this.form.submit()"/>
      </form>
      <a href="?m=bloc&a=ajax_vw_planning_ressources&operation_id={{$operation->_id}}&type_ressource_id={{$type_ressource_id}}&besoin_ressource_id={{$besoin_ressource_id}}&date={{$date_after}}&dialog=1" style="display: inline;">&gt;&gt;&gt;</a>
      </span>
    </th>
  </tr>
  <tr>
    <th></th>
    {{foreach from=$hours item=_hour}}
      <th>{{$_hour|date_format:"%H"}}h</th>
    {{/foreach}}
  </tr>
  {{foreach from=$ressources item=_ressource key=ressource_id name=ressources}}
    <tr>
      <th style="height: {{if $smarty.session.browser.name == "firefox"}}28{{else}}23{{/if}}px;">
        {{$_ressource}}
        {{if $besoin_ressource_id}}
          <button type="button" class="tick notext" style="float: right;" onclick="createUsage('{{$ressource_id}}')"></button>
        {{/if}}
      </th>
      {{foreach from=$hours item=_hour name=hours}}
        <td>
          {{if $smarty.foreach.hours.first}}
            {{*
              Dans la toute première cellule, on place l'intervention concernée
             *}}
            {{if $smarty.foreach.ressources.first}}
              {{math equation=x*y x=$operation->_debut_offset y=$td_width assign=offset}}
              {{math equation=x*y x=$operation->_width y=$td_width assign=width}}
              
              <div class="planning_ressource interv"
                style="position: absolute; left: {{$offset}}%; width: {{$width}}%; height: {{$height}}px;"></div>
            {{/if}}
            {{*
              Dans la première case, on place les usages et les indisponibilités 
             *}}
            {{if isset($usages_by_ressource.$ressource_id|smarty:nodefaults)}}
              {{foreach from=$usages_by_ressource.$ressource_id item=_usage}}
                {{math equation=x*y x=$_usage->_debut_offset y=$td_width assign=offset}}
                {{math equation=x*y x=$_usage->_width y=$td_width assign=width}}
                <div class="planning_ressource usage{{if $_usage->_id == $usage_ressource_id}}_selected{{/if}}"
                  style="position: absolute; left: {{$offset}}%; width: {{$width}}%;"></div>
              {{/foreach}}
            {{/if}}
            {{if isset($indispos_by_ressource.$ressource_id|smarty:nodefaults)}}
              {{foreach from=$indispos_by_ressource.$ressource_id item=_indispo}}
                {{math equation=x*y x=$_indispo->_debut_offset y=$td_width assign=offset}}
                {{math equation=x*y x=$_indispo->_width y=$td_width assign=width}}
                <div class="planning_ressource indispo"
                  style="position: absolute; left: {{$offset}}%; width: {{$width}}%;"></div>
              {{/foreach}}
            {{/if}}
          {{/if}}
        </td>
      {{/foreach}}
    </tr>
  {{/foreach}}
</table>