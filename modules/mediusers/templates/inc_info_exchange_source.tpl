{{* $Id: inc_edit_user.tpl 8378 2010-03-18 15:15:48Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 8378 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">  
  <tr>
    <th class="title" colspan="2">
      {{tr}}config-exchange-source{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="category">{{tr}}CExchangeSource.smtp-desc{{/tr}}</th>
    <th class="category">{{tr}}CExchangeSource.pop-desc{{/tr}}</th>
  </tr>
  <tr>
    <td style="width:50%;"> {{mb_include module=system template=inc_config_exchange_source source=$smtp_source}} </td>
    <td style="width:50%;">
      <script type="text/javascript">
        Main.add(function () {
          Control.Tabs.create('tabs-sources-pop');
        });
      </script>


      <table class="main">
        <tr>
          <td style="vertical-align: top;" class="narrow">
            <ul id="tabs-sources-pop" class="control_tabs_vertical">
            {{foreach from=$sources_pop item=source_pop}}
              <li>
                <a href="#source_pop_{{$source_pop->_guid}}">
                  {{$source_pop->libelle}}
                </a>
              </li>
            {{/foreach}}
            </ul>
          </td>
          <td style="vertical-align: top;">
          {{foreach from=$sources_pop item=source_pop}}
            <div id="source_pop_{{$source_pop->_guid}}" style="display:none;">
              {{mb_include module=system template=inc_config_exchange_source source=$source_pop}}
            </div>
          {{/foreach}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
		