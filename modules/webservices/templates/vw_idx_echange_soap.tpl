{{* $Id: vw_idx_echange_hprim.tpl 6287 2009-05-13 15:37:54Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

</script>

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
            <th class="category" colspan="2">Choix de la date d'échange</th>
          </tr>
          <tr>
            <th>{{mb_label object=$echange_soap field="_date_min"}}</th>
            <td class="date">{{mb_field object=$echange_soap field="_date_min" form="filterEchange" register=true}}</td>
          </tr>
          <tr>
             <th>{{mb_label object=$echange_soap field="_date_max"}}</th>
             <td class="date">{{mb_field object=$echange_soap field="_date_max" form="filterEchange" register=true}}</td>
          </tr>
          <tr>
            <th class="category" colspan="2">Critères de filtres</th>
          </tr>
          <tr>
            <th>{{mb_label object=$echange_soap field="echange_soap_id"}}</th>
            <td>{{mb_field object=$echange_soap field="echange_soap_id"}}</td>
          </tr>
          <tr>
            <th>Types de services</th>
            <td>
              <select class="str" name="web_service">
                <option value="">&mdash; Liste des web services </option>
                {{foreach from=$dPconfig.webservices.webservice|static:"services" key=_service_name item=_service_libelle}}
								  <option value="{{$_service_name}}" {{if $web_service == $_service_name}}selected="selected"{{/if}}>
                    {{$_service_libelle}}
                  </option>
								{{/foreach}}
              </select>
            </td>
          </tr>
					 <tr>
            <th>Fonctions</th>
            <td>
            	<select class="str" name="fonction">
                <option value="">&mdash; Liste des fonctions </option>
                
              </select>
						</td>
				  </tr>
          <tr>
            <td colspan="2" style="text-align: center">
              <button type="submit" class="search">Filtrer</button>
            </td>
          </tr>
        </table>
        {{if $total_echange_soap != 0}}
	        <div style="font-weight:bold;padding-top:10px"> 
	          {{$total_echange_soap}} {{tr}}results{{/tr}}
	        </div>
	        <div class="pagination">
	          {{if ($page == 1)}}
	            {{$page}}
	          {{else}}
	            <a href="#1" onclick="$V(document.forms.filterEchange.elements.page, {{$page-1}})"> < Précédent </a> |
	            <a href="#1" onclick="$V(document.forms.filterEchange.elements.page, 1)"> 1 </a> | 
	            {{$page}} 
	          {{/if}}
	          {{if $page != $total_pages}}
	            | <a href="#1" onclick="$V(document.forms.filterEchange.elements.page, {{$total_pages}})"> {{$total_pages}} </a> | 
	            <a href="#1" onclick="$V(document.forms.filterEchange.elements.page, {{$page+1}})"> Suivant > </a>
	          {{/if}}
	        </div>
	        <div>
	          <select name="listpageeechangesoap" onchange="$V(this.form.elements.page, $V(this))">
	            <option value="">&mdash; Page</option>
	            {{if $total_pages < 4}}
	              {{assign var="step" value=1}}
	            {{else}}
	              {{assign var="step" value=4}}
	            {{/if}}
	            {{foreach from=1|range:$total_pages:$step item=_page}}
	              <option value="{{$_page}}" {{if $_page == $page}}selected="selected"{{/if}}>{{$_page}}</option>
	            {{/foreach}}
	          </select>
	        </div>
	      {{/if}}
      </form>
    </td>
  </tr>
  
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="14">ECHANGES SOAP</th>
        </tr>
        <tr>
          <th></th>
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
        {{foreach from=$listEchangeSoap item=curr_echange_soap}}
          <tbody id="echange_{{$curr_echange_soap->_id}}">
            {{include file="inc_echange_soap.tpl" object=$curr_echange_soap}}
          </tbody>
        {{foreachelse}}
          <tr>
            <td colspan="14">
              {{tr}}CEchangeHprim.none{{/tr}}
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
            ECHANGE SOAP - {{$echange_soap->echange_soap_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
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
      </table>
    </td>
  </tr>
  {{/if}}
</table>