{{* $Id: configure.tpl 8217 2010-03-05 10:49:05Z phenxdesign $ *}}
 
{{*
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function() {
    Control.Tabs.create("tabs_modeles", true);
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

<form name="editConfig" action="?m={{$m}}&{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <ul class="control_tabs" id="tabs_modeles">
    <li>
      <a href="#modeles">Modèles</a>
    </li>
    <li>
      <a href="#acces">Accès</a>
    </li>
    <li>
      <a href="#tools">Outils</a>
    </li>
  </ul>

  <div id="modeles">
    {{mb_include template=CCompteRendu_config}}
  </div>
  <div id="acces">
    {{mb_include template=CCompteRendu_acces_config}}
  </div>
  <div id="tools">
    {{mb_include template=CCompteRendu_tools_config}}
  </div>
</form>
