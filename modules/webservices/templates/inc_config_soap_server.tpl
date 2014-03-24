{{*
 * Configure SOAP server EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<table class="form">
  <tr>
    <th class="title">
      {{tr}}config-soap-server{{/tr}}
    </th>
  </tr>

  <tr>
    <td>
      <form name="editConfig-webservices" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="dosql" value="do_configure" />
        <input type="hidden" name="m" value="system" />
        <table class="form">

          {{mb_include module=system template=inc_config_str var=wsdl_root_url}}

          <tr>
            <td class="button" colspan="10">
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>

  <tr>
    <th class="title">
      {{tr}}config-exchange-source{{/tr}}
    </th>
  </tr>
  <tr>
    <td> {{mb_include module=system template=inc_config_exchange_source source=$mb_soap_server}} </td>
  </tr>
</table>