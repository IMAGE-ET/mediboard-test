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
      }, 10000);
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
      {{tr}}{{$exchange->_class}}{{/tr}}
    </th>
  </tr>
  <!-- Filtres -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="filterExchange" method="get" onsubmit="return ExchangeDataFormat.refreshExchangesList(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="types[]" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit()"/>
        <input type="hidden" name="exchange_class" value="{{$exchange->_class}}" />
        <input type="hidden" name="order_col" value="date_production" />
        <input type="hidden" name="order_way" value="DESC" />

        <table class="main layout">
          <tr>
            <td class="separator expand" onclick="MbObject.toggleColumn(this, $(this).next())"></td>

            <td>
              <table class="main form">
                <tr>
                  <th style="width: 15%">{{mb_label object=$exchange field=date_echange}}</th>
                  <td class="text" style="width: 35%">
                    {{mb_field object=$exchange field=_date_min register=true form="filterExchange" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
                    <b>&raquo;</b>
                    {{mb_field object=$exchange field=_date_max register=true form="filterExchange" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
                  </td>

                  <th style="width: 15%">{{mb_label object=$exchange field="group_id"}}</th>
                  <td style="width: 35%">
                    {{mb_field object=$exchange field="group_id" canNull=true form="filterExchange" autocomplete="true,1,50,true,true"
                    placeholder="Tous les établissements"}}
                  </td>
                </tr>

                <tr>
                  <th>{{mb_label object=$exchange field="object_id"}}</th>
                  <td>{{mb_field object=$exchange field="object_id" placeholder="Identifiant de l'objet"}}</td>

                  <th>{{mb_label object=$exchange field="id_permanent"}}</th>
                  <td>{{mb_field object=$exchange field="id_permanent" placeholder="IPP/NDA"}}</td>
                </tr>


                <tr>
                  <th>{{mb_label object=$exchange field=type}}</th>
                  <td>
                    <select class="str" name="type" onchange="ExchangeDataFormat.fillSelect(this, this.form.elements.evenement, '{{$mod_name}}')">
                      <option value="">&mdash; Messages &mdash;</option>
                      {{foreach from=$messages key=_message item=_class_message}}
                        <option value="{{$_message}}" {{if $exchange->type == $_message}}selected="selected"{{/if}}> {{tr}}{{$mod_name}}-msg-{{$_message}}{{/tr}}</option>
                      {{/foreach}}
                    </select>
                  </td>

                  <th>{{mb_label object=$exchange field=sous_type}}</th>
                  <td>
                    <select class="str" name="evenement">
                      <option value="">&mdash; Événements &mdash;</option>
                    </select>
                  </td>
                </tr>

                <tr>
                  <th>{{mb_label object=$exchange field=_message}}</th>
                  <td>
                    <input type="text" name="keywords_msg" value="{{$keywords_msg}}" placeholder="Mots-clés dans le message" size="40px"/>
                  </td>

                  <th>{{mb_label object=$exchange field=_acquittement}}</th>
                  <td>
                    <input type="text" name="keywords_ack" value="{{$keywords_ack}}" placeholder="Mots-clés dans l'acquittement" size="40px"/>
                  </td>
                </tr>

                {{mb_include module=$mod_name template="`$exchange->_class`_filter_inc"}}

                <tr>
                  <th>{{tr}}Filter{{/tr}}</th>
                  <td colspan="3">
                    {{foreach from=$types key=status_type item=_type}}
                      <fieldset style="display: inline-block;
                        background-color: {{if $status_type == "error"}}rgba(255, 102, 102, 0.4){{else}}rgba(148, 221, 137, 0.4){{/if}} !important; margin-top: 0;">
                        {{foreach from=$_type key=type item=value}}
                          <label>
                            <input onclick="$V(this.form.page, 0)" type="checkbox" name="types[{{$type}}]"/>
                            {{tr}}CExchange-type-{{$type}}{{/tr}}
                          </label>
                        {{/foreach}}
                      </fieldset>
                    {{/foreach}}
                  </td>
                </tr>

                <tr>
                  <td colspan="4">
                    <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
                  </td>
                </tr>
              </table>
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