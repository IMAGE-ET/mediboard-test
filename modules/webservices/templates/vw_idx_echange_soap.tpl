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
        
        <table class="form">
          <tr>
            <th class="category" colspan="2">Choix de la date d'échange</th>
          </tr>
          <tr>
            <th>{{mb_label object=$echange_soap field="_date_min"}}</th>
            <td class="date">{{mb_field object=$echange_soap field="_date_min" form="filterEchange" register=true}} </td>
          </tr>
          <tr>
             <th>{{mb_label object=$echange_soap field="_date_max"}}</th>
             <td class="date">{{mb_field object=$echange_soap field="_date_max" form="filterEchange" register=true}} </td>
          </tr>
          <tr>
            <th class="category" colspan="2">Critères de filtres</th>
          </tr>
          <tr>
            <th>Types de services</th>
            <td>
              <select class="str" name="web_service">
                <option value="">&mdash; Liste des web services </option>
                <option value="SigeGateConf" {{if $web_service == "SigeGateConf"}}selected="selected"{{/if}}>
                  Configuration
                </option>
                <option value="SigeGateDico" {{if $web_service == "SigeGateDico"}}selected="selected"{{/if}}>
                  Dictionnaire
                </option>
                <option value="SigeGatePat" {{if $web_service == "SigeGatePat"}}selected="selected"{{/if}}>
                  Patient
                </option>
                <option value="SigeGateDosBase" {{if $web_service == "SigeGateDosBase"}}selected="selected"{{/if}}>
                  Dossier Administratif
                </option>
                <option value="SigeGateInterv" {{if $web_service == "SigeGateInterv"}}selected="selected"{{/if}}>
                  Intervention
                </option>
                <option value="SigeGateSej" {{if $web_service == "SigeGateSej"}}selected="selected"{{/if}}>
                  Séjour
                </option>
                <option value="SigeGateActeCcam" {{if $web_service == "SigeGateActeCcam"}}selected="selected"{{/if}}>
                  Acte CCAM
                </option>
                <option value="SigeGateDiag" {{if $web_service == "SigeGateDiag"}}selected="selected"{{/if}}>
                  Diagnostic - CIM10
                </option>
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: center">
              <button type="submit" class="search">Filtrer</button>
            </td>
          </tr>
        </table>
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