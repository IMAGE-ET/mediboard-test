{{if $coreModules|@count}}
  <div class="big-warning">
    Un ou plusieurs des modules de base ne sont pas à jour.<br />
    Des erreurs risquent de s'afficher et le système ne fonctionnera pas correctement.<br />
    Veuillez les mettre à jour afin de supprimer ces erreurs résultantes et avoir accès aux autres modules
  </div>
  {{assign var="modules" value=$coreModules}}
  {{assign var="modFiles" value=null}}
{{/if}}

<script type="text/javascript">

var Templates = {
  id: "Templates",
  
  empty: function() {
    var url = new Url;
    url.setModuleAction("system", "httpreq_do_empty_templates");
    url.requestUpdate(this.id);
  }
}

var SharedMemory = {
  id : "SharedMemory", 
  
  empty: function(ie) {
    var url = new Url;
    url.setModuleAction("system", "httpreq_do_empty_shared_memory");
    url.requestUpdate(this.id);
  },
  
  check: function(ie) {
    var url = new Url;
    url.setModuleAction("system", "httpreq_check_shared_memory");
    url.requestUpdate(this.id);
  }
}


function pageMain() {
  SharedMemory.check("SharedMemory");
}

</script>

<h2>Administration des modules</h2>

<table border="0" cellpadding="2" cellspacing="1" width="98%" class="tbl">
  <tr>
    <th colspan="2">{{tr}}Module{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
    <th>{{tr}}Type{{/tr}}</th>
    <th>{{tr}}Version{{/tr}}</th>
    <th>{{tr}}Menu Text{{/tr}}</th>
    <th>{{tr}}Menu Status{{/tr}}</th>
    <th>#</th>
  </tr>
  {{foreach from=$modules item=module}}
  <tr>
    <td>
      <img alt="updown" src="./images/icons/updown.gif" width="10" height="15" border=0 usemap="#arrow{{$module.mod_id}}" />
      {{if $can->edit}}
      <map name="arrow{{$module.mod_id}}">
        <area coords="0,0,10,7"  href="{{$module.href}}&amp;cmd=moveup" />
        <area coords="0,8,10,14" href="{{$module.href}}&amp;cmd=movedn" />
      </map>
      {{/if}}
    </td>
    <td width="1%" nowrap="nowrap">{{$module.mod_name}}</td>
    <td>
      {{if $module.mod_active}}
        {{assign var="src" value="./images/icons/dotgreen.gif"}}
      {{else}}
        {{assign var="src" value="./images/icons/dotyellowanim.gif"}}
      {{/if}}
      <img alt="dot" src="{{$src}}" width="12" height="12" />
      {{if $can->edit}}
        <a class="action" href="{{$module.href}}&amp;cmd=toggle">
      {{/if}}
      {{if $module.mod_active}}
        {{tr}}active{{/tr}}
      {{else}}
        {{tr}}disabled{{/tr}}
      {{/if}}
      {{if $can->edit}}
        </a>
        |
        <a class="action" href="{{$module.href}}&amp;cmd=remove"
        onclick="return window.confirm('{{tr}}This will delete all data associated with the module!{{/tr}}\n\n{{tr}}Are you sure?{{/tr}}\n')">
          {{tr}}remove{{/tr}}
        </a>
        {{if $module.is_setup && !$module.is_upToDate}}
        |
        <a class="action" href="{{$module.href}}&amp;cmd=upgrade"
        onclick="return window.confirm('{{tr}}Are you sure?{{/tr}}')">
          {{tr}}upgrade{{/tr}}
        </a>
        {{/if}}
        {{if $module.is_config}}
        |
        <a class="action" href="{{$module.href}}&amp;cmd=configure">
          {{tr}}configure{{/tr}}
        </a>
        {{/if}}
      {{/if}}
    </td>
    <td>{{$module.mod_type}}</td>
    <td>{{$module.mod_version}}</td>
    <td>{{tr}}module-{{$module.mod_name}}-long{{/tr}}</td>
    <td>
      {{if $module.mod_ui_active}}
        {{assign var="src" value="./images/icons/dotgreen.gif"}}
      {{else}}
        {{assign var="src" value="./images/icons/dotredanim.gif"}}
      {{/if}}
      <img alt="dot" src="{{$src}}" width="12" height="12" />
      {{if $can->edit}}
      <a class="action" href="{{$module.href}}&amp;cmd=toggleMenu">
      {{/if}}
      {{if $module.mod_ui_active}}
        {{tr}}visible{{/tr}}
      {{else}}
        {{tr}}hidden{{/tr}}
      {{/if}}
      {{if $can->edit}}
      </a>
      {{/if}}
    </td>
    <td>{{$module.mod_ui_order}}</td>
  </tr>
  {{/foreach}}
  {{foreach from=$modFiles|smarty:nodefaults item=module}}
  <tr>
    <td></td>
    <td>{{$module}}</td>
    <td>
      <img alt="not installed" src="./images/icons/dotgrey.gif" width="12" height="12" />
      {{if $can->edit}}
        <a class="action" href="?m={{$m}}&amp;a=domodsql&amp;cmd=install&amp;mod_name={{$module}}">
          {{tr}}install{{/tr}}
        </a>
      {{/if}}
    </td>
  </tr>
  {{/foreach}}
</table>

<h2>Nettoyage du cache</h2>

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