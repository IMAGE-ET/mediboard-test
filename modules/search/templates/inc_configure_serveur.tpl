{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="main" id="table_main">
  <tr>
    <td>
      <form name="EditConfig-Search" action="?m={{$m}}&tab=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_configure" />
        <table class="form">

          <tr>
            <th class="category" colspan="2">{{tr}}CSearch-indexing server{{/tr}}</th>
          </tr>

          {{mb_include module=system template=inc_config_str var=client_host}}
          {{mb_include module=system template=inc_config_str var=client_port}}
          {{mb_include module=system template=inc_config_str var=index_name}}
          {{mb_include module=system template=inc_config_str var=nb_replicas}}
          {{mb_include module=system template=inc_config_str var=interval_indexing}}

          <tr>
            <th class="category" colspan="2">{{tr}}CSearch-search server{{/tr}}</th>
          </tr>

          {{mb_include module=system template=inc_config_str var=ids_search}}
          <tr>
            <th class="category" colspan="2">{{tr}}CSearch-extract server{{/tr}}</th>
          </tr>

          {{mb_include module=system template=inc_config_str var=tika_host}}
          {{mb_include module=system template=inc_config_str var=tika_port}}

          <tr>
            <td class="button" colspan="2">
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>