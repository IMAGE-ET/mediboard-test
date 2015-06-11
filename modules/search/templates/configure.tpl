{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage search
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}
{{mb_script module=search script=Search}}

<script>
  Main.add(function () {
    var tabs = Control.Tabs.create('tabs-configure', true, {
      afterChange :function(container){
        switch(container.id){
          case "CConfigEtab"    : Configuration.edit('search', 'CGroups', container.id); break;
          case "CConfigServeur" : Search.configServeur(); break;
          case "CConfigES"      : Search.configES(); break;
          case "CConfigReIndexing" : Search.configReIndexing(); break;
          default : Configuration.edit('search', 'CGroups', container.id); break;
        }
      }
    });
  });
</script>

<table class="main">
  <tr>
    <td>
      <ul id="tabs-configure" class="control_tabs">
        <li><a href="#CConfigEtab">{{tr}}CConfigEtab{{/tr}}</a></li>
        <li><a href="#CConfigServeur">Config serveur</a></li>
        <li><a href="#CConfigES">Config ES</a></li>
        <li><a href="#CConfigReIndexing">Réindexation</a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td>
      <div id="CConfigEtab" style="display: none"></div>
      <div id="CConfigServeur" style="display: none"></div>
      <div id="CConfigES" style="display: none"></div>
      <div id="CConfigReIndexing" style="display: none"></div>
    </td>
  </tr>
</table>