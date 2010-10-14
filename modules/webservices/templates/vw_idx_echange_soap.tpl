{{* $Id: vw_idx_echange_hprim.tpl 6287 2009-05-13 15:37:54Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$echange_soap->_id}}
<script type="text/javascript">
	function fillSelect(select, dest) {
    var url = new Url("webservices", "ajax_filter_web_func");
    url.addParam("service_demande", select);
    url.addParam("type", dest);

    if (dest == 'fonction') {
      url.addParam("web_service_demande", $V(getForm('filterEchange').web_service));
    }
    
    url.requestUpdate(dest, {onComplete: function() {
      if (dest == 'web_service') {
        fillSelect($V(getForm('filterEchange').service), 'fonction');
      }
    }});
	}

	function changePage(page) {
	  $V(getForm('filterEchange').page, page);
	}
</script>
{{/if}}

{{if !$echange_soap->_id}}
  {{main}}
    fillSelect($V(getForm('filterEchange').service), 'web_service');
  {{/main}}
{{/if}}

<div id="empty_area" style="display: none;"></div>
<table class="main">
  {{if !$echange_soap->_id}}
  
  <!-- Filtres -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="filterEchange" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.submit()"/>
        
        <table class="form">
          <tr>
            <th class="category" colspan="4">Choix de la date d'échange</th>
          </tr>
          <tr>
            <th style="width:50%">{{mb_label object=$echange_soap field="_date_min"}}</th>
            <td class="narrow">{{mb_field object=$echange_soap field="_date_min" form="filterEchange" register=true}}</td>
            <th class="narrow">{{mb_label object=$echange_soap field="_date_max"}}</th>
            <td style="width:50%">{{mb_field object=$echange_soap field="_date_max" form="filterEchange" register=true}}</td>
          </tr>
          <tr>
            <th class="category" colspan="4">Critères de filtres</th>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$echange_soap field="echange_soap_id"}}</th>
            <td colspan="2">{{mb_field object=$echange_soap field="echange_soap_id"}}</td>
          </tr>
          <tr>
            <th colspan="2">Types de services</th>
            <td colspan="2">
              <select class="str" name="service" onchange="fillSelect(this.value, 'web_service')">
                <option value="">&mdash; Liste des types de services</option>
								{{foreach from=$services item=_service}}
								  <option value="{{$_service}}" {{if $service == $_service}} selected="selected"{{/if}}>
								    {{$_service}}
								  </option>
								{{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <th colspan="2">Webservices</th>
            <td colspan="2">
              <select class="str" id = "web_service" name="web_service" onchange="fillSelect($V(getForm('filterEchange').service), 'fonction')">
                <option value="">&mdash; Liste des web services</option>
              </select>
            </td>
          </tr>
					 <tr>
            <th colspan="2">Fonctions</th>
            <td colspan="2">
            	<select class="str" name="fonction" id="fonction">
                <option value="">&mdash; Liste des fonctions</option>
	            </select>
						</td>
				  </tr>
          <tr>
            <td colspan="4" style="text-align: center">
              <button type="submit" class="search" onclick="$V(getForm('filterEchange').page, 0);">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
        </table>
        {{if $total_echange_soap != 0}}
          {{mb_include module=system template=inc_pagination total=$total_echange_soap current=$page change_page='changePage' jumper='10'}}
	      {{/if}}
      </form>
    </td>
  </tr>
  
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="15">{{tr}}CEchangeSOAP{{/tr}}</th>
        </tr>
        <tr>
          <th></th>
          <th style="width:0.1px;"></th>
          <th>{{mb_title object=$echange_soap field="echange_soap_id"}}</th>
          <th>{{mb_title object=$echange_soap field="date_echange"}}</th>
          <th>{{mb_title object=$echange_soap field="emetteur"}}</th>
          <th>{{mb_title object=$echange_soap field="destinataire"}}</th>
          <th>{{mb_title object=$echange_soap field="type"}}</th>
          <th>{{mb_title object=$echange_soap field="web_service_name"}}</th>
          <th>{{mb_title object=$echange_soap field="function_name"}}</th>
          <th>{{mb_title object=$echange_soap field="input"}}</th>
          <th>{{mb_title object=$echange_soap field="output"}}</th>
          <th>{{mb_title object=$echange_soap field="response_time"}}</th>
        </tr>
        {{foreach from=$echangesSoap item=curr_echange_soap}}
          <tbody id="echange_{{$curr_echange_soap->_id}}">
            {{include file="inc_echange_soap.tpl" object=$curr_echange_soap}}
          </tbody>
        {{foreachelse}}
          <tr>
            <td colspan="15">
              {{tr}}CEchangeSOAP.none{{/tr}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{else}}
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="form">
        <tr>
          <th class="title" colspan="2">
            {{tr}}CEchangeSOAP{{/tr}} - {{$echange_soap->_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
            <br />
            {{mb_value object=$echange_soap field="function_name"}}
          </th>
        </tr>
        <tr>
          <th class="category">{{mb_title object=$echange_soap field="input"}}</th>
          <th class="category">{{mb_title object=$echange_soap field="output"}}</th>
        </tr>
        <tr>
          <td style="width: 50%"> {{mb_value object=$echange_soap field="input" export=true}} </td>
          <td> {{mb_value object=$echange_soap field="output" export=true}} </td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center;">
            <a target="blank" href="?m=webservices&a=download_echange&echange_soap_id={{$echange_soap->_id}}&dialog=1&suppressHeaders=1&message=1&acq=1" class="button modify">Télécharger</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{/if}}
</table>