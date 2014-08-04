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
  Main.add(function () {
    Control.Tabs.create("tabs-configure", true, {
      afterChange: function(container) {
        ExchangeSource.SourceAvailability(container);
      }
    })
  });

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

  function editSource(guid, source_class) {
    new Url("eai", "ajax_edit_source")
      .addParam("source_guid", guid)
      .requestModal(600)
      .modalObject.observe("afterClose", function() {
        new Url("eai", "ajax_refresh_exchange_sources")
          .addParam("source_class", source_class)
          .requestUpdate(source_class+"_sources", function() {
            ExchangeSource.SourceAvailability($('tab'+source_class));
          });
      });
  }
</script>

<ul id="tabs-configure" class="control_tabs small">
{{foreach from=$all_sources key=name item=_sources}}
  <li>
    <a href="#tab{{$name}}">
      {{tr}}{{$name}}{{/tr}} ({{$_sources|@count}})
    </a>
  </li>
{{/foreach}}
</ul>

{{foreach from=$all_sources key=name item=_sources}}
  <div id="tab{{$name}}" style="display: none;">
    <table class="tbl">
      <tr>
        <th class="section" style="width: 30%">
          {{tr}}CExchangeSource-name{{/tr}}
        </th>
        <th class="section" style="width: 15%">
          {{tr}}CExchangeSource-libelle{{/tr}}
        </th>
        <th class="section">
        </th>
        <th class="section">
          {{tr}}Time-response{{/tr}}
        </th>
        <th class="section">
          {{tr}}Message{{/tr}}
        </th>
      </tr>
      <tbody id="{{$name}}_sources">
        {{mb_include module=eai template=inc_vw_sources}}
      </tbody>
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
