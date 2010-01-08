{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main"> 
  <tr>
    <td>
      <form name="ConfigDSN-{{$ftpsn}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="dosql" value="do_configure" />
        <input type="hidden" name="m" value="system" />       
        <table class="form">
          <!-- Configure dsn '{{$ftpsn}}' -->
          {{assign var="section" value="ftp"}}
        
          <tr>
            <th class="title" colspan="100">
              {{tr}}config-{{$section}}{{/tr}} '{{$ftpsn}}'
              {{assign var=ftpsnConfig value=0}}
              {{if $ftpsn|array_key_exists:$dPconfig.$section}}
                {{assign var=ftpsnConfig value=$dPconfig.$section.$ftpsn}}
              {{/if}} 
            </th>
          </tr>
          
          <tr>
            {{assign var="var" value="ftphost"}}
            <th>
              <label for="{{$section}}[{{$ftpsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
                {{tr}}config-{{$section}}-{{$var}}{{/tr}}
              </label>  
            </th>
            <td>
              {{mb_ternary test=$ftpsnConfig var=value value=$ftpsnConfig.$var other=""}}
              <input type="text" name="{{$section}}[{{$ftpsn}}][{{$var}}]" value="{{$value}}" />
            </td>
          </tr>
          
          <tr>
            {{assign var="var" value="ftpuser"}}
            <th>
              <label for="{{$section}}[{{$ftpsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
                {{tr}}config-{{$section}}-{{$var}}{{/tr}}
              </label>  
            </th>
            <td>
              {{mb_ternary test=$ftpsnConfig var=value value=$ftpsnConfig.$var other=""}}
              <input type="text" name="{{$section}}[{{$ftpsn}}][{{$var}}]" value="{{$value}}" />
            </td>
          </tr>
          
          <tr>
            {{assign var="var" value="ftppass"}}
            <th>
              <label for="{{$section}}[{{$ftpsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
                {{tr}}config-{{$section}}-{{$var}}{{/tr}}
              </label>  
            </th>
            <td>
              {{mb_ternary test=$ftpsnConfig var=value value=$ftpsnConfig.$var other=""}}
              <input type="text" name="{{$section}}[{{$ftpsn}}][{{$var}}]" value="{{$value}}" />
            </td>
          </tr>
          
          <tr>
            {{assign var="var" value="port"}}
            <th>
              <label for="{{$section}}[{{$ftpsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
                {{tr}}config-{{$section}}-{{$var}}{{/tr}}
              </label>  
            </th>
            <td>
              {{mb_ternary test=$ftpsnConfig var=value value=$ftpsnConfig.$var other=""}}
              <input type="text" name="{{$section}}[{{$ftpsn}}][{{$var}}]" value="{{$value}}" />
            </td>
          </tr>
          
          <tr>
            {{assign var="var" value="timeout"}}
            <th>
              <label for="{{$section}}[{{$ftpsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
                {{tr}}config-{{$section}}-{{$var}}{{/tr}}
              </label>  
            </th>
            <td>
              {{mb_ternary test=$ftpsnConfig var=value value=$ftpsnConfig.$var other=""}}
              <input type="text" name="{{$section}}[{{$ftpsn}}][{{$var}}]" value="{{$value}}" />
            </td>
          </tr>
          
          <tr>
            {{assign var="var" value="pasv"}}
            <th>
              <label for="{{$section}}[{{$ftpsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
                {{tr}}config-{{$section}}-{{$var}}{{/tr}}
              </label>  
            </th>
            <td>
              <label for="{{$section}}[{{$ftpsn}}][{{$var}}]_0">{{tr}}config-{{$section}}-{{$var}}-0{{/tr}}</label>
              <input type="radio" name="{{$section}}[{{$ftpsn}}][{{$var}}]" value="0" {{if $ftpsnConfig.$var == "0"}}checked="checked"{{/if}}/>
              <label for="{{$section}}[{{$ftpsn}}][{{$var}}]_1">{{tr}}config-{{$section}}-{{$var}}-1{{/tr}}</label>
              <input type="radio" name="{{$section}}[{{$ftpsn}}][{{$var}}]" value="1" {{if $ftpsnConfig.$var == "1"}}checked="checked"{{/if}}/>
            </td>
          </tr>
        
          <tr>
            {{assign var="var" value="mode"}}
            <th>
              <label for="{{$section}}[{{$ftpsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
                {{tr}}config-{{$section}}-{{$var}}{{/tr}}
              </label>  
            </th>
            <td>
              {{mb_ternary test=$ftpsnConfig var=value value=$ftpsnConfig.$var other=""}}
              <select name="{{$section}}[{{$ftpsn}}][{{$var}}]">
                <option value="FTP_ASCII"  {{if "FTP_ASCII"  == $value}} selected="selected" {{/if}}>{{tr}}config-{{$section}}-{{$var}}-FTP_ASCII{{/tr}}</option>
                <option value="FTP_BINARY" {{if "FTP_BINARY" == $value}} selected="selected" {{/if}}>{{tr}}config-{{$section}}-{{$var}}-FTP_BINARY{{/tr}}</option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              {{mb_ternary test=$ftpsnConfig var=button_text value=Modify other=Create}}
              {{mb_ternary test=$ftpsnConfig var=button_class value=modify other=new}}
              <button class="{{$button_class}}" type="submit">{{tr}}{{$button_text}}{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td class="greedyPane">
      <script type="text/javascript">
        var FTPSN = {
          test: function (sFTPSN) {
            var url = new Url;
            url.setModuleAction("system", "ajax_test_ftpsn");
            url.addParam("ftpsn", sFTPSN);
            url.requestUpdate("config-admin-ftpsn-test-" + sFTPSN);
          }
        }
      </script>
      <table class="tbl">
        <tr>
          <th class="title" colspan="100">
            {{tr}}config-admin-ftpsn{{/tr}} '{{$ftpsn}}'
          </th>
        </tr>
        
        <!-- Test socket FTPSN -->
        <tr>
          <td>
            <button type="button" class="search" onclick="FTPSN.test('{{$ftpsn}}');">
              {{tr}}config-admin-ftpsn-test{{/tr}}
            </button>
          </td>
          <td id="config-admin-ftpsn-test-{{$ftpsn}}" />
        </tr>
      </table>
    </td>
  </tr>
</table>