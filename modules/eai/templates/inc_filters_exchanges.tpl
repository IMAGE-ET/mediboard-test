{{*
 * Filters Exchanges Data Format EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
ExchangeDataFormat.evenements = {{$evenements|@json}};
</script>

{{assign var=mod_name value=$exchange->_ref_module->mod_name}}

<table class="main">
  <tr>
    <th class="title">
      <button onclick="ExchangeDataFormat.toggle();" style="float: left;" class="hslip notext" type="button" title="{{tr}}CExchangeDataFormat{{/tr}}">
        {{tr}}CExchangeDataFormat{{/tr}}
      </button>
      {{tr}}{{$exchange->_class_name}}{{/tr}}</th>
  </tr>
  <!-- Filtres -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="filterExchange" method="get" onsubmit="return ExchangeDataFormat.refreshExchangesList(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="types[]" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit()"/>
        <input type="hidden" name="exchange_class_name" value="{{$exchange->_class_name}}" />
        
        <table class="form">
          <tr>
            <th class="category" colspan="4">Choix de la date d'échange</th>
          </tr>
          <tr>
            <th style="width:50%">{{mb_label object=$exchange field="_date_min"}}</th>
            <td class="narrow">{{mb_field object=$exchange field="_date_min" form="filterExchange" register=true onchange="\$V(this.form.page, 0)"}} </td>
            <th class="narrow">{{mb_label object=$exchange field="_date_max"}}</th>
            <td style="width:50%">{{mb_field object=$exchange field="_date_max" form="filterExchange" register=true onchange="\$V(this.form.page, 0)"}} </td>
          </tr>
          <tr>
            <th class="category" colspan="4">{{tr}}filter-criteria{{/tr}}</th>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$exchange field=$exchange->_spec->key}}</th>
            <td colspan="2">{{mb_field object=$exchange field=$exchange->_spec->key}}</td>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$exchange field="id_permanent"}}</th>
            <td colspan="2">{{mb_field object=$exchange field="id_permanent"}}</td>
          </tr>
          
          <tr>
            <th colspan="2">Type de message</th>
            <td colspan="2">
              <select class="str" name="type" onchange="ExchangeDataFormat.fillSelect(this, this.form.elements.evenement, '{{$mod_name}}')">
                <option value="">&mdash; Messages &mdash;</option>
                {{foreach from=$messages key=_message item=_class_message}}
                  <option value="{{$_message}}"> {{tr}}{{$mod_name}}-msg-{{$_message}}{{/tr}}</option>
                {{/foreach}}
              </select>
            </td>
          </tr> 
          <tr>
            <th colspan="2">Types d'événements</th>
            <td colspan="2">
              <select class="str" name="evenement">
                <option value="">&mdash; Événements &mdash;</option>
                <option value="inconnu"> {{tr}}{{$mod_name}}-evt-choose{{/tr}} </option>
              </select>
            </td>
          </tr>
          
          {{mb_include module=$mod_name template="`$exchange->_class_name`_filter_inc"}}
          
          <tr>
            <td colspan="4" style="text-align: center">
              {{foreach from=$types key=type item=value}}
                <input onclick="$V(this.form.page, 0)" type="checkbox" name="types[{{$type}}]"/>{{tr}}CExchange-type-{{$type}}{{/tr}}
              {{/foreach}}
            </td>
          </tr>
          
          <tr>
            <td colspan="4" style="text-align: center">
              <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  
  <tr>
    <td class="halfPane" rowspan="3" id="exchangesList">
    </td>
  </tr>
</table>