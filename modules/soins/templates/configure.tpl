{{*  *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  redirectOffline = function(type, embed) {
    switch(type) {
      case 'sejour':
        var url = new Url("soins", "offline_sejours");
        break;
      case 'bilan':
        var url = new Url("soins", "offline_bilan_service");
    }
    
    url.addParam("service_id", $("service_id").value);
    url.addParam("dialog", 1);

    if (embed) {
      url.addParam("embed", 1);
      url.addParam("_aio", "savefile");
      url.pop(500, 400, "Vue embarquée");
    }
    else {
      url.redirect();
    }
  }

  Main.add(function () {
    var tabs = Control.Tabs.create('tabs-configure', true);
    if (tabs.activeLink.key == "CConfigEtab") {
      Configuration.edit('soins', ['CGroups', 'CService CGroups.group_id'], $('CConfigEtab'));
    }
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li>
    <a href="#soins">Dossier de soins</a>
  </li>
  <li onmousedown="Configuration.edit('soins', 'CGroups', $('CConfigEtab'))">
    <a href="#CConfigEtab">Config par établissement</a>
  </li>
</ul>

<div id="soins" style="display: none">
  {{mb_include module=soins template=inc_configure_soins}}
</div>

<div id="CConfigEtab" style="display: none"></div>