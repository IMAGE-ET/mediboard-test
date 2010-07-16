{{* $Id: vw_idx_echange_hprim.tpl 6287 2009-05-13 15:37:54Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	var methods = {{$methods|@json}};
	
	function fillSelectWeb(source, dest) {
	  var selected = $V(source);
	  dest.update();
    dest.insert("<option value=''>&mdash; Liste des web services</option>");
    if(selected in methods) {
		  for(web in methods[selected]) {
        dest.insert(new Element('option', {value: web}).update(web));
	  	}
    }
    var form = getForm("filterEchange");
    fillSelectFunc(form.elements.web_service, form.elements.service, form.elements.fonction);
	}

  function fillSelectFunc(source, intermed, dest) {
    var selectedA = $V(source);
    var selectedB = $V(intermed);
    dest.update();
    dest.insert("<option value=''>&mdash; Liste des fonctions</option>");
    if(selectedB in methods && selectedA in methods[selectedB]) {
      methods[selectedB][selectedA].each(function(v){
        dest.insert(new Element('option', {value: v}).update(v));
      });
    }
  }

	function changePage(page) {
	  $V(getForm('filterEchange').page,page);
	}
</script>
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
            <td style="width:0.1%">{{mb_field object=$echange_soap field="_date_min" form="filterEchange" register=true}}</td>
            <th style="width:0.1%">{{mb_label object=$echange_soap field="_date_max"}}</th>
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
              <select class="str" name="service" onchange="fillSelectWeb(this, this.form.elements.web_service)">
                <option value="">&mdash; Liste des types de services</option>
                {{foreach from=$methods key=_service_name item=_type}}
                  <option value="{{$_service_name}}" {{if $service == $_service_name}} selected="selected"{{/if}}>
                    {{tr}}{{$_service_name}}{{/tr}}
                  </option>
                {{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <th colspan="2">Webservices</th>
            <td colspan="2">
              <select class="str" name="web_service" onchange="fillSelectFunc(this, this.form.elements.service, this.form.elements.fonction)">
                <option value="">&mdash; Liste des web services</option>
                {{if $web_service == '' || array_key_exists($web_service, $methods.$service)}}
                  {{foreach from=$methods.$service key=_web_service_name item=_web_service}}
                    <option value="{{$_web_service_name}}" {{if $web_service == $_web_service_name}}selected="selected"{{/if}}>
                      {{$_web_service_name}}
                    </option>
                  {{/foreach}}
                {{/if}}
              </select>
                
              </select>
            </td>
          </tr>
					 <tr>
            <th colspan="2">Fonctions</th>
            <td colspan="2">
            	<select class="str" name="fonction">
                <option value="">&mdash; Liste des fonctions</option>
                {{if ($fonction == '' || in_array($fonction, $methods.$service.$web_service)) && $web_service != ''}}
                  {{foreach from=$methods.$service.$web_service key=_method_name item=_method}}
                    <option value="{{$_method}}" {{if $fonction == $_method}}selected="selected"{{/if}}>
                      {{$_method}}
                    </option>
                  {{/foreach}}
                {{/if}}
						</td>
				  </tr>
          <tr>
            <td colspan="4" style="text-align: center">
              <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
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
          <th class="title" colspan="14">{{tr}}CEchangeSOAP{{/tr}}</th>
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
        </tr>
        {{foreach from=$echangesSoap item=curr_echange_soap}}
          <tbody id="echange_{{$curr_echange_soap->_id}}">
            {{include file="inc_echange_soap.tpl" object=$curr_echange_soap}}
          </tbody>
        {{foreachelse}}
          <tr>
            <td colspan="14">
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
          <td style="width: 50%">
            {{mb_value object=$echange_soap field="input" export=true}}
          </td>
          <td>
            {{mb_value object=$echange_soap field="output" export=true}}
          </td>
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