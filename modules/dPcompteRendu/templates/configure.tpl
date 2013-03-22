{{* $Id: configure.tpl 8217 2010-03-05 10:49:05Z phenxdesign $ *}}
 
{{*
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create("tabs_modeles");
  });
  
  restoreModeleId = function(modele_id, do_it, auto) {
    var url = new Url("dPcompteRendu", "ajax_restore_link");
    url.addParam("modele_id", modele_id);
    url.addParam("do_it", do_it);
    url.requestUpdate("result_restore", {
      insertion: function(element, content){
        window.save_content = content;
        element.innerHTML += content;
      },
      onComplete: function() {
        if (auto) {
          var form = getForm("editConfig");
          var elt = form.modele_id;
          if (elt.selectedIndex == (elt.length - 1)) return;
          var converted = 0, total = 0;
          if (window.save_content != "" && do_it && window.save_content.indexOf("/") != -1) {
            converted = parseInt(window.save_content.split("/")[0]);
            total = parseInt(window.save_content.split("/")[1]);
          }
          if (converted == total) {
              form.modele_id.selectedIndex++;
            }
          form.button_restore.click();
        }
      }
    });
  }
</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <ul class="control_tabs" id="tabs_modeles">
    <li>
      <a href="#modeles">Modèles</a>
    </li>
    <li>
      <a href="#tools">Outils</a>
    </li>
  </ul>
  
  <hr class="control_tabs" />
  
  <div id="modeles">
    <table class="form">
      <col style="width: 50%"/>
      {{assign var="class" value="CCompteRendu"}}
      <tr>
        <th class="category" colspan="2">
          {{tr}}config-dPcompteRendu-CCompteRendu-print{{/tr}}
        </th>
      </tr>
    
      {{if $dompdf_installed || $wkhtmltopdf_installed}}
        {{assign var="var" value="pdf_thumbnails"}}
        {{mb_include module=system template=inc_config_bool}}
        {{assign var="var" value="same_print"}}
        {{mb_include module=system template=inc_config_bool}}
        {{assign var="var" value="time_before_thumbs"}}
        {{mb_include module=system template=inc_config_str numeric=1}}
      {{else}}
        <tr>
          <td colspan="2">
            <div class="small-error">Aucune librairie de conversion PDF n'est installée</div>
          </td>
        </tr>
      {{/if}}
      
      <tr>
        <th class="category" colspan="2">{{tr}}config-dPcompteRendu-CCompteRendu-correspondants{{/tr}}</th>
      </tr>
      <tr>
        {{assign var="var" value="multiple_doc_correspondants"}}
        {{mb_include module=system template=inc_config_bool}}
      </tr>
      
      <tr>
        <th class="category" colspan="2">{{tr}}config-dPcompteRendu-CCompteRendu-other_params{{/tr}}</th>
      </tr>
      <tr>
        {{assign var="var" value="default_font"}}
        {{mb_include module=system template=inc_config_enum values="Arial|Calibri|Comic Sans MS|Courier New|Georgia|Lucida Sans Unicode|Symbol|Tahoma|Times New Roman|Trebuchet MS|Verdana|ZapfDingBats"}}
      </tr>
      <tr>
        {{assign var="var" value="default_size"}}
        {{mb_include module=system template=inc_config_enum values="xx-small|x-small|small|medium|large|x-large|xx-large|8pt|9pt|10pt|11pt|12pt|14pt|16pt|18pt|20pt|22pt|24pt|26pt|28pt|36pt|48pt|72pt"}}
      </tr>
      <tr>
        {{assign var="var" value="header_footer_fly"}}
        {{mb_include module=system template=inc_config_bool}}
      </tr>
      <tr>
        {{assign var="var" value="clean_word"}}
        {{mb_include module=system template=inc_config_bool}}
      </tr>
      <tr>
        {{assign var="var" value="check_to_empty_field"}}
        {{mb_include module=system template=inc_config_bool}}
      </tr>
      <tr>
        {{assign var="var" value="arch_wkhtmltopdf"}}
        {{mb_include module=system template=inc_config_enum values=i386|amd64}}
      </tr>
      {{if !$can_64bit}}
        <tr>
          <td colspan="2">
            <div class="warning" style="float: right;">
              Le serveur n'est pas compatible pour exécuter la version 64 bit de wkhtmltoPDF
            </div>
          </td>
        </tr>
      {{/if}}
      <tr>
        {{assign var="var" value="dompdf_host"}}
        {{mb_include module=system template=inc_config_bool}}
      </tr>
      {{assign var="var" value="days_to_lock"}}
      {{assign var="var_item" value="base"}}
      <tr>
        <th>
          <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}-desc{{/tr}}">
            {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}
          </label>
        </th>
        <td>
          <input type="text" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="{{$conf.$m.$class.$var.$var_item}}"/>
        </td>
      </tr>
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
                var field = DateFormat.format(new Date(), timestamp.value).replace(/%p/g, User.view.split(" ")[1]);
                field = field.replace(/%n/g, User.view.split(" ")[0]);
                field = field.replace(/%i/g, User.view.split(" ")[1].charAt(0) + ". " + User.view.split(" ")[0].charAt(0) + ". ");
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
      
      <tr>
        <td class="button" colspan="2">
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    </table>
  </div>
  <div id="tools">
    <table class="tbl">
      <tr>
        <th colspan="2">
          Association des documents aux modèles respectifs
        </th>
      </tr>
      <tr>
        <td style="vertical-align: top;">
          <select name="modele_id">
            {{foreach from=$modeles item=_modele}}
              <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
            {{/foreach}}
          </select>
          <button type="button" name="button_restore" class="tick notext" onclick="restoreModeleId($V(this.form.modele_id), this.form.do_it.checked ? 1 : 0, this.form.auto.checked)"></button>
          <label>
            <input type="checkbox" name="auto"/> Auto
          </label>
          <label>
            <input type="checkbox" name="do_it"/> Réel
          </label>
        </td>
        <td id="result_restore" class="text"></td>
      </tr>
    </table>
  </div>
</form>
