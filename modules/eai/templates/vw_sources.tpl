{{*
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=system script=exchange_source}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));

  refreshExchangeSource = function(exchange_source_name, type){
    var url = new Url("system", "ajax_refresh_exchange_source");
    url.addParam("exchange_source_name", exchange_source_name);
    url.addParam("type", type);
    url.requestUpdate('exchange_source-'+exchange_source_name);
  };

  Echange = {
    purge: function(force, source_class) {
      var form = getForm('EchangePurge'+source_class);

      if (!force && !$V(form.auto)) {
        return;
      }

      if (!checkForm(form)) {
        return;
      }

      new Url('system', 'ajax_purge_echange')
        .addFormData(form)
        .requestUpdate("purge-echange-"+source_class);
    }
  }
</script>

<ul id="tabs-configure" class="control_tabs">
{{foreach from=$all_sources key=name item=_sources}}
  <li><a href="#tab{{$name}}">{{tr}}{{$name}}{{/tr}}</a></li>
{{/foreach}}
</ul>

{{foreach from=$all_sources key=name item=_sources}}
  <div id="tab{{$name}}" style="display: none;">
    <table class="tbl">
      <tr>
        <th>
          {{tr}}Name{{/tr}}
        </th>
        <th>
          {{tr}}Reachable{{/tr}}
        </th>
        <th>
          {{tr}}Message{{/tr}}
        </th>
        <th>
          {{tr}}Time-response{{/tr}}
        </th>
        <th>
          {{tr}}Number-exchange{{/tr}}
        </th>
      </tr>
      {{foreach from=$_sources name=boucle_source item=_source}}
        <tr>
          <td class="narrow">
            <a href="#" onclick="ExchangeSource.editSource('{{$_source->_guid}}');" title="Modifier la source">
              {{$_source->name}}
            </a>
          </td>
          <td class="narrow">
            {{unique_id var=uid}}
            {{main}}
              ExchangeSource.dispoSource($('{{$uid}}'));
            {{/main}}
            <img class="status" id="{{$uid}}" data-id="{{$_source->_id}}"
                 data-guid="{{$_source->_guid}}" src="images/icons/status_grey.png"
                 title="{{$_source->name}}"/>
          </td>
          <td class="text compact">
            {{$_source->_message|smarty:nodefaults}}
          </td>
          <td class="narrow">
            {{$_source->_response_time}}
          </td>
          {{if $smarty.foreach.boucle_source.first}}
            <td class="narrow" style="text-align: center" rowspan="{{$smarty.foreach.boucle_source.total}}">
              {{$count_exchange.$name}}
            </td>
          {{/if}}
        </tr>
      {{foreachelse}}
        <tr>
          <td colspan="5" class="empty">
            {{tr}}{{$name}}.none{{/tr}}
          </td>
        </tr>
    {{/foreach}}
    </table>
    {{if $_sources|@count > 0 && $name === "CSourceSOAP" || $name === "CSourceFTP"}}
    <br/>
    <table class="form">
      <tr>
        <th class="title">{{tr}}Purge{{/tr}}</th>
      </tr>
      <tr>
        <td>
          {{mb_include module=system template=inc_purge_echange source_class=$name}}
        </td>
      </tr>
    </table>
    {{/if}}
  </div>
{{/foreach}}
