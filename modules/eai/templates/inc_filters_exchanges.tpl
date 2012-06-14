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
  ExchangeDataFormat.evenements = {{"utf8_encode"|array_map_recursive:$evenements|@json:true}};
  toggleAutoRefresh = function(){
    if (!window.autoRefresh) {
      window.autoRefresh = setInterval(function(){
        getForm("filterExchange").onsubmit();
      }, 5000);
      $("auto-refresh-toggler").style.borderColor = "red";
    }
    else {
      clearTimeout(window.autoRefresh);
          window.autoRefresh = null;
      $("auto-refresh-toggler").style.borderColor = "";
    }
  }
</script>

{{assign var=mod_name value=$exchange->_ref_module->mod_name}}

<table class="main">
  <tr>
    <th class="title">
      <button onclick="ExchangeDataFormat.toggle();" style="float: left;" class="hslip notext" type="button" title="{{tr}}CExchangeDataFormat{{/tr}}">
        {{tr}}CExchangeDataFormat{{/tr}}
      </button>
      <button onclick="toggleAutoRefresh()" id="auto-refresh-toggler" style="float: right;" class="change notext" type="button">
        Auto-refresh (5s)
      </button>
      {{tr}}{{$exchange->_class}}{{/tr}}</th>
  </tr>
  <!-- Filtres -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="filterExchange" method="get" onsubmit="return ExchangeDataFormat.refreshExchangesList(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="types[]" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit()"/>
        <input type="hidden" name="exchange_class" value="{{$exchange->_class}}" />
        
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
            <th colspan="2">{{mb_label object=$exchange field="group_id"}}</th>
            <td colspan="2">{{mb_field object=$exchange field="group_id" canNull=true form="filterExchange" autocomplete="true,1,50,true,true"}}</td>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$exchange field=$exchange->_spec->key}}</th>
            <td colspan="2">{{mb_field object=$exchange field=$exchange->_spec->key}}</td>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$exchange field="object_id"}}</th>
            <td colspan="2">{{mb_field object=$exchange field="object_id"}}</td>
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
                  <option value="{{$_message}}" {{if $exchange->type == $_message}}selected="selected"{{/if}}> {{tr}}{{$mod_name}}-msg-{{$_message}}{{/tr}}</option>
                {{/foreach}}
              </select>
            </td>
          </tr> 
          <tr>
            <th colspan="2">Types d'événements</th>
            <td colspan="2">
              <select class="str" name="evenement">
                <option value="">&mdash; Événements &mdash;</option>
              </select>
            </td>
          </tr>
          
          <tr>
            <th colspan="2">Mots-clés dans le contenu</th>
            <td colspan="2">
              <input type="text" name="keywords" value="{{$keywords}}" />
            </td>
          </tr>
          
          {{mb_include module=$mod_name template="`$exchange->_class`_filter_inc"}}
          
          <tr>
            <td colspan="4" style="text-align: center">
              {{foreach from=$types key=type item=value}}
                <label>
                  <input onclick="$V(this.form.page, 0)" type="checkbox" name="types[{{$type}}]"/> 
                  {{tr}}CExchange-type-{{$type}}{{/tr}}
                </label>
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