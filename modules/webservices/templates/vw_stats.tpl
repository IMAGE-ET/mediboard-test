{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
var series = {{$series|@json}};
var options = {{$options|@json}};

function fillSelect(select, dest) {
  var url = new Url("webservices", "ajax_filter_web_func");
  url.addParam("service_demande", select);
  url.addParam("type", dest);
  if (dest == 'fonction') {
    url.addParam("web_service_demande", $V(getForm('formStat').web_service));
  }
  url.requestUpdate(dest, {onComplete: function() {
    if (dest == 'web_service') {
      fillSelect($V(getForm('formStat').service), 'fonction');
    }
  }});
}

Main.add(function(){  
  var oFormStat = getForm("formStat");
  Calendar.regField(oFormStat.date_min);
  Calendar.regField(oFormStat.date_max);

  {{if $service}}
    Flotr.draw($('graph'), series, options);
  
    fillSelect('{{$service}}', 'web_service');
  {{/if}}
});
</script>

<form name="formStat" method="get" action="?">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
  <table class="form">
    <tr>
      <th class="title" colspan="6">{{tr}}stats-echanges-soap{{/tr}}</th>
    </tr>
    <tr>
      <th class="category"></th>
      <th class="category">{{tr}}early{{/tr}}</th>
      <th class="category">{{tr}}end{{/tr}}</th>
      <th class="category">{{tr}}CEchangeSOAP-type{{/tr}}</th>
      <th class="category">{{tr}}CEchangeSOAP-web_service_name{{/tr}}</th>
      <th class="category">{{tr}}CEchangeSOAP-function_name{{/tr}}</th>
    </tr>
    <tr>
      <td class="button narrow"><button class="search" type="submit" >{{tr}}Filter{{/tr}}</button></td>
      <td>  
        <input type="hidden" name="date_min" value="{{$date_min}}" />
      </td>
      <td>
        <input type="hidden" name="date_max" value="{{$date_max}}" />
      </td>
      <td>
        <select class="str" name="service" onchange="fillSelect(this.value, 'web_service')">
          <option value="">&mdash; Liste des types de services</option>
          {{foreach from=$services item=_service}}
            <option value="{{$_service}}" {{if $service == $_service}} selected="selected"{{/if}}>
              {{$_service}}
            </option>
          {{/foreach}}
        </select>
      </td>
      <td>
        <select class="str" id="web_service" name="web_service" 
           onchange="fillSelect($V(getForm('formStat').service), 'fonction')">
          <option value="">&mdash; Liste des web services</option>
        </select>
      </td>
      <td>
        <select class="str" id="fonction" name="fonction">
          <option value="">&mdash; Liste des fonctions</option>
        </select>
      </td>
    </tr>
  </table>
</form>

<div style="height: 400px; margin: 1em;" id="graph"></div>