{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="system" script="object_selector"}}

<script>
  function changePage(start) {
    $V(getForm("filterFrm").start, start);
  }

  function setStats(val) {
    $V($("filterFrm").stats, val);
  }

  function setCsv(val) {
    $V($("filterFrm").csv, val);
    if (val == '1') {
      $("filterFrm").target = "_blank";
      $V($("filterFrm").suppressHeaders, '1');
      $V($(filterFrm).ajax , '1');
      $("filterFrm").submit();
    }
    $V($("filterFrm").csv, '0');
    $("filterFrm").target = "";
    $V($(filterFrm).ajax , '0');
    $V($("filterFrm").suppressHeaders, '0');
  }

  function emptyClass(form) {
    $V(form.object_class, 0);
    $V(form.object_class.up('td').down('input'), '');
    $V(form.object_id, '');
  }

  Main.add(function() {
    var form = getForm("filterFrm");
    form.getElements().each(function(e){
      e.observe("change", function(){
        $V(form.start, 0);
      });
    });

    $(form.object_class).makeAutocomplete({width: "200px"});

    $V(form.period, '{{$period}}', false);

    ObjectSelector.init = function() {
      this.sForm     = "filterFrm";
      this.sId       = "object_id";
      this.sView     = "object_id";
      this.sClass    = "object_class";
      this.onlyclass = "false";
      this.pop();
    }

    // Autocomplete des users
    var element = form._view;
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element.name);
    url.autoComplete(element, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.user_id, id);
        if ($V(element) == "") {
          $V(element, selected.down('.view').innerHTML);
        }
      }
    });
  });
</script>

<form name="filterFrm" id="filterFrm" action="?" method="get" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="ajax" value="0" />
  <input type="hidden" name="suppressHeaders" value="0" />
  <input type="hidden" name="dialog" value="{{$dialog}}" />
  <input type="hidden" name="start" value="{{$start|default:0}}" onchange="this.form.submit()" />
  <input type="hidden" name="stats" value="{{$stats}}" />
  <input type="hidden" name="csv" value="{{$csv}}" />
  <input type="hidden" name="user_id" value="{{$filter->user_id}}" />

  <table class="form">
    <tr>
      <th>{{mb_label object=$filter field=user_id}}</th>
      <td>
        <input type="text" name="_view" class="autocomplete" placeholder="&mdash; Tous" value="{{$filter->_ref_user}}" />
        <button type="button" class="cancel notext" onclick="$V(this.form.user_id, ''); $V(this.form._view, '');"></button>
      </td>

      <th>{{mb_label object=$filter field=object_class}}</th>
      <td>
        <select name="object_class" class="str" style="width: 200px;">
          <option value="">&mdash; Toutes les classes</option>
          {{foreach from=$listClasses item=curr_class}}
            <option value="{{$curr_class}}" {{if $curr_class == $filter->object_class}}selected{{/if}}>
              {{tr}}{{$curr_class}}{{/tr}} - {{$curr_class}}
            </option>
          {{/foreach}}
        </select>
        <button type="button" class="cancel notext" onclick="emptyClass(this.form)"></button>
      </td>

      <th>{{mb_label object=$filter field="_date_min"}}</th>
      <td>{{mb_field object=$filter field="_date_min" form="filterFrm" register=true}}</td>

    </tr>
    <tr>
      <th>{{mb_label object=$filter field=type}}</th>
      <td>{{mb_field object=$filter field=type canNull=true emptyLabel="Choose"}}</td>

      <th>{{mb_label object=$filter field=object_id}}</th>
      <td>
        {{mb_field object=$filter field=object_id canNull=true}}
        <button type="button" class="search" onclick="ObjectSelector.init()">Chercher un objet</button>
      </td>
      <th>{{mb_label object=$filter field="_date_max"}}</th>
      <td>{{mb_field object=$filter field="_date_max" form="filterFrm" register=true}}</td>
    </tr>
    <tr>
      <td class="button" colspan="5">
        <button class="search" onclick="setStats('0'); setCsv('0');">{{tr}}Search{{/tr}}</button>
        <button class="lookup" onclick="setStats('1')">{{tr}}Statistics{{/tr}}</button>
        <button type="button" class="download" onclick="setCsv('1');">csv</button>
        <label for="period" title="Période">{{tr}}Period{{/tr}}</label>
        <select name="period" onchange="this.form.submit();">
          <option value="hour" >{{tr}}Hour{{/tr}} </option>
          <option value="day"  >{{tr}}Day{{/tr}}  </option>
          <option value="week" >{{tr}}Week{{/tr}} </option>
          <option value="month">{{tr}}Month{{/tr}}</option>
          <option value="year" >{{tr}}Year{{/tr}} </option>
        </select>
      </td>
    </tr>
  </table>
</form>

{{if !$stats}}
  {{mb_include module=system template=inc_pagination total=$list_count current=$start step=100 change_page='changePage' jumper=1}}
{{/if}}