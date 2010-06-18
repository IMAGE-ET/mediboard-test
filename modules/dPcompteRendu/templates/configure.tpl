{{* $Id: configure.tpl 8217 2010-03-05 10:49:05Z phenxdesign $ *}}
 
{{*
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

<table class="form">
  <col style="width: 50%"/>
  {{assign var="class" value="CCompteRendu"}}
  <tr>
    <th class="category" colspan="2">
      {{tr}}config-dPcompteRendu-CCompteRendu-print{{/tr}}
    </th>
  </tr>

  {{if $dompdf_installed}}
    {{assign var="var" value="pdf_thumbnails"}}
    {{mb_include module=system template=inc_config_bool}}
    {{assign var="var" value="same_print"}}
    {{mb_include module=system template=inc_config_bool}}
  {{else}}
    <tr>
      <td colspan="2">
        <div class="small-error">La librairie DOMPDF n'est pas installée</div>
      </td>
    </tr>
  {{/if}}
  {{assign var=aide_autocomplete value=$dPconfig.dPcabinet.CConsultation.aide_autocomplete}}
  {{if $aide_autocomplete == 1}}
    <tr>
      <th class="category" colspan="2">
        Horodatage pour les aides à la saisie
      </th>
    </tr>
    {{assign var="var" value="timestamp"}}
    {{mb_include module=system template=inc_config_str}}
    <tr>
      <td></td>
      <td>
        <div>
          <script type="text/javascript">
            var timestamp = getForm("editConfig")["dPcompteRendu[CCompteRendu][timestamp]"];
            var reloadfield = function() {
              var field = DateFormat.format(new Date(), timestamp.value).replace(/%p/g, User.view.split(" ")[0]);
              field = field.replace(/%n/g, User.view.split(" ")[1]);
              field = field.replace(/%i/g, User.view.split(" ")[0].charAt(0) + ". " + User.view.split(" ")[1].charAt(0) + ". ");
              $('preview').innerHTML = field;
            };
            var addfield = function(name) {
              timestamp.value += name + " ";
              reloadfield();
            }
            Main.add(function() {
              (timestamp.up()).insert({bottom: "<div style='display: inline;' id='preview'></div>"});
              timestamp.observe('keyup', reloadfield);
              reloadfield();
            });
          </script>
          <table>
            <tr><td><a href="#1" onclick="addfield('dd');">dd</a></td><td>{{tr}}config-dPcompteRendu-CCompteRendu-day{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('MM');">MM</a></td><td>{{tr}}config-dPcompteRendu-CCompteRendu-month{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('y');" >y</a></td><td>{{tr}}config-dPcompteRendu-CCompteRendu-yearlong{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('yy');">yy</a></td><td>{{tr}}config-dPcompteRendu-CCompteRendu-yearshort{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('HH');">HH</a></td><td>{{tr}}config-dPcompteRendu-CCompteRendu-hourlong{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('hh');">hh</a></td><td>{{tr}}config-dPcompteRendu-CCompteRendu-hourshort{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('mm');">mm</td><td>{{tr}}config-dPcompteRendu-CCompteRendu-minute{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('ss');">ss</td><td>{{tr}}config-dPcompteRendu-CCompteRendu-second{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('a');" >a</td><td>{{tr}}config-dPcompteRendu-CCompteRendu-meridian{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('%p');">%p</td><td>{{tr}}config-dPcompteRendu-CCompteRendu-name_firstname{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('%n');">%n</td><td>{{tr}}config-dPcompteRendu-CCompteRendu-name_lasttname{{/tr}}</td></tr>
            <tr><td><a href="#1" onclick="addfield('%i');">%i</td><td>{{tr}}config-dPcompteRendu-CCompteRendu-name_initials{{/tr}}</td></tr>
          </table>
        </div>
      </td>
    </tr>
  {{/if}}
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</form>
