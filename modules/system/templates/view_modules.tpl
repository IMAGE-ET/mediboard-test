{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
Main.add(function () {
  Control.Tabs.create('tabs-modules', true);
});
</script>

{{if $coreModules|@count}}
  <div class="big-warning">
    Un ou plusieurs des modules de base ne sont pas à jour.<br />
    Des erreurs risquent de s'afficher et le système ne fonctionnera pas correctement.<br />
    Veuillez les mettre à jour afin de supprimer ces erreurs résultantes et avoir accès aux autres modules
  </div>
	{{include file="inc_module.tpl" object=$coreModules}}
{{else}}
	<ul id="tabs-modules" class="control_tabs">
	  <li><a {{if $upgradable}}class="wrong"{{/if}} {{if !$mbmodules.installe|@count}}class="empty"{{/if}} href="#installed">{{tr}}CModule-modules-installed{{/tr}} ({{$mbmodules.installe|@count}})</a></li>
	  <li><a {{if !$mbmodules.aInstaller|@count}}class="empty"{{/if}} href="#notInstalled">{{tr}}CModule-modules-notInstalled{{/tr}} ({{$mbmodules.aInstaller|@count}})</a></li>
    <li><a href="#cache">{{tr}}module-system-cache{{/tr}}</a></li>
    <li><a {{if $majLibs|@count}}class="wrong"{{/if}} href="#libs">{{tr}}module-system-libs{{/tr}} {{if $majLibs|@count}}({{$majLibs|@count}}){{/if}}</a></li>
    <li><a  href="#assistant">{{tr}}module-system-assistant{{/tr}}</a></li>
	</ul>
	
	<hr class="control_tabs" />
	
	<div id="installed" style="display: none;">
		{{include file="inc_module.tpl" object=$mbmodules.installe installe=true}}
	</div>
	
	<div id="notInstalled" style="display: none;">
	  {{include file="inc_module.tpl" object=$mbmodules.aInstaller installe=false}}
	</div>
  
  <div id="cache" style="display: none;">
    <script type="text/javascript">
      function updateControlTabs() {
        var tab = $$('a[href="#cache"]').first();
        if ($(this.id).select('.error, .warning').length)
          tab.addClassName('wrong');
        else 
          tab.removeClassName('wrong');
      }
      
      var Templates = {
        id: "Templates",
        
        empty: function() {
          var url = new Url("system", "httpreq_do_empty_templates");
          url.requestUpdate(this.id);
        }
      }
      
      var SharedMemory = {
        id : "SharedMemory", 
        
        empty: function(ie) {
          var url = new Url("system", "httpreq_do_empty_shared_memory");
          url.requestUpdate(this.id, {onComplete: updateControlTabs.bind(this)} );
        },
        
        check: function(ie) {
          var url = new Url("system", "httpreq_check_shared_memory");
          url.requestUpdate(this.id, {onComplete: updateControlTabs.bind(this)} );
        }
      }
      
      Main.add(function () {
        SharedMemory.check("SharedMemory");
      });
      
      </script>
      
      <table class="tbl">
        <tr>
          <th>Action</th>
          <th>Status</th>
        </tr>
        <tr>
          <td>
            <button class="cancel" onclick="Templates.empty()">
              Vider les caches template Smarty
            </button>
          </td>
          <td id="Templates" />
        </tr>
        <tr>
          <td>
            <button class="cancel" onclick="SharedMemory.empty(); ">
              Vider les variables de la mémoire partagée
            </button>
          </td>
          <td id="SharedMemory" />
        </tr>
      </table>
  </div>
  
  <div id="libs" style="display: none;">
    <table class="tbl">
      <tr>
        <th>Action</th>
        <th>Status</th>
      </tr>
      <tr>
        <td>
          <button class="change" onclick="document.location.href='install/install.php'">
            Mettre à jour les bibliothèques externes
          </button>
        </td>
        <td>
          {{if $majLibs|@count}}
          <div class='error'>
            {{$majLibs|@count}} bibliothèques à mettre à jour <br />
            {{foreach from=$majLibs item=_lib}}
              - {{$_lib}} <br />
            {{/foreach}}
          </div>
         {{else}} 
           <div class='message'>Bibliothèques externes à jour</div>
         {{/if}}
        </td>
      </tr>
    </table>
  </div>
  
  <div id="assistant" style="display: none;">
    {{include file="view_install.tpl"}} 
  </div>
{{/if}}