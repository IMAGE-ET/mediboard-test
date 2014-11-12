{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module=search script=Search ajax=true}}
{{if $error == "mapping"}}
  <div class="small-error"> Un problème est survenu pour la création de l'index. Vérifiez l'état du service ElasticSearch</div>
{{/if}}
{{if $error == "index"}}
  <div class="small-error"> Un problème est survenu pour l'indexation des données. Vérifiez l'état du service ElasticSearch</div>
{{/if}}
<form name="EditConfig-ES" action="?m={{$m}}&tab=configure" method="get" onsubmit="return onSubmitFormAjax(this)">
  <table class="main tbl" id="tab_config_es">
    <tr>
      <th class="title"> Indexation générique</th>
      <th class="title"> Indexation des recherches (logs)</th>
    </tr>
    <tbody id="first_indexing">
    <tr>
      <td class="halfPane">
        {{mb_include module=search template=inc_configure_es_generique}}
      </td>
      <td class="halfPane" style="vertical-align: top">
        {{mb_include module=search template=inc_configure_es_log}}
      </td>
    </tr>
    </tbody>
  </table>
</form>
