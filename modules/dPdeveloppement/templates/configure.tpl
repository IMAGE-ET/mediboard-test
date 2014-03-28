{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
Main.add(function(){
  Control.Tabs.create("ide-integration-tabs", true);
});
</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    <col style="width: 50%" />
    <tr>
      <th class="title" colspan="2">
        Intégration de l'IDE
      </th>
    </tr>

    <tr>
      <td colspan="2">
        <ul class="control_tabs" id="ide-integration-tabs">
          <li><a href="#ide-url">Par URL personnalisée</a></li>
          <li><a href="#ide-path">Par le protocole IDE</a></li>
        </ul>
      </td>
    </tr>

    <tbody id="ide-url">
      <tr>
        <td colspan="2">
          <div class="small-info">
            Le <a href="http://plugins.jetbrains.com/plugin/6027" target="_blank">plugin RemoteCall pour PhpStorm</a> permet de
            spécifier une URL pour ouvrir les fichiers, avec l'URL suivante : <code>http://localhost:8091?message=%file%</code>
          </div>
        </td>
      </tr>
      {{mb_include module=system template=inc_config_str var=ide_url size=70}}
    </tbody>

    <tbody id="ide-path">
      <tr>
        <td colspan="2">
          <div class="small-info">
            Les IDE/éditeurs suivants sont officiellement supportés:
            <ul>
              <li>Notepad++</li>
              <li>PhpStorm</li>
              <li>Sublime Text</li>
            </ul>
          </div>

          Afin de permettre l'ouverture de l'IDE depuis Mediboard, il faut suivre les étapes suivantes:

          <ol>
            <li>Ajouter le chemin vers l'éxécutable de <code>PHP</code> dans le PATH si ce n'est pas déjà le cas.</li>
            <li>Enregistrer le chemin vers Mediboard dans la variable d'environnement <code>MEDIBOARD_PATH</code> (<code>{{$path}}</code>)</li>
            <li>Spécifier ci-dessous le chemin vers l'éxecutable de l'IDE (ou le nom de l'éxécutable s'il est dans le PATH)</li>
            <li>
              <strong>Sous Windows</strong>:
              <ol>
                <li>
                  Executer le fichier
                  <code><a href="dev/register_protocol/register_ide.reg" target="_blank" download>register_ide.reg</a></code>
                </li>
                <li>Redémarrer</li>
              </ol>
            </li>
            <li>
              <strong>Sous Linux</strong>:
              <div class="small-warning">
                En cours de développement.
              </div>
              {{*
              <ol>
                <li>
                  Exécuter les lignes suivantes:
                  <pre>
  gconftool-2 -t string -s /desktop/gnome/url-handlers/ide/command 'php $MEDIBOARD_PATH/dev/ide.php "%s"'
  gconftool-2 -s /desktop/gnome/url-handlers/ide/needs_terminal false -t bool
  gconftool-2 -s /desktop/gnome/url-handlers/ide/enabled true -t bool
                  </pre>
                </li>
              </ol>
  *}}
            </li>
            <li>
              <strong>Sous MacOS</strong>:
              <!-- https://support.shotgunsoftware.com/entries/127152 -->
              <div class="small-warning">
                En cours de développement.
              </div>
            </li>
          </ol>
        </td>
      </tr>
      {{mb_include module=system template=inc_config_str var=ide_path size=70}}
    </tbody>

    <tr>
      <th class="title" colspan="2">
        Profilage XHProf
      </th>
    </tr>
    <tr>
      <td colspan="2">
        <div class="small-info">
          Chemin du projet <a href="https://github.com/staabm/xhprof.io/" target="_blank">XHProf.io</a>.<br />
          Suivre les <a href="https://github.com/staabm/xhprof.io/blob/master/INSTALL.md" target="_blank">instructions d'installation</a>
          auparavant pour permettre l'utilisation du CMbProfiler.<br />
          <pre>
$profiler = new CMbProfiler();
$profiler->start();

// Code à profiler ...

$profiler->stop();</pre>
        </div>
      </td>
    </tr>

    {{mb_include module=system template=inc_config_str var=profiling_xhprof_path size=70}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
