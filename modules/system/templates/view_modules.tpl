{{if $coreModules|@count}}
  <div class="big-warning">
    Un ou plusieurs des modules de base ne sont pas à jour.<br />
    Des erreurs risquent de s'afficher et le système ne fonctionnera pas correctement.<br />
    Veuillez les mettre à jour afin de supprimer ces erreurs résultantes et avoir accès aux autres modules
  </div>
  {{assign var=mbmodules value=$coreModules}}
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

Main.add(function () {
  SharedMemory.check("SharedMemory");
});

</script>

<h2>Administration des modules</h2>

<table class="tbl">
  <tr>
    <th colspan="2">{{mb_title class=CModule field=mod_name}}</th>
    <th>{{mb_title class=CModule field=_view}}</th>
    <th>{{tr}}Status{{/tr}}</th>
    <th>{{tr}}Upgrade{{/tr}} ?</th>
    <th>{{tr}}Version{{/tr}}</th>
    <th>{{mb_title class=CModule field=mod_active}}</th>
    <th>{{mb_title class=CModule field=mod_ui_active}}</th>
    <th>#</th>
  </tr>
  
  {{foreach from=$mbmodules item=mbmodule}}

  {{if !$mbmodule->_id}}
  {{assign var=module_name value=$mbmodule->mod_name}}
  {{assign var=cmd value="?m=system&a=domodsql&mod_name=$module_name&cmd"}}
  <tr>
    <td></td>

    <td><strong>{{$mbmodule->mod_name}}</strong></td>

    <td>
    	<label title="{{tr}}module-{{$mbmodule->mod_name}}-long{{/tr}}">
    	  {{tr}}module-{{$mbmodule->mod_name}}-court{{/tr}}
    	</label>
    </td>

    <td>{{$mbmodule->mod_type}}</td>

    <td colspan="10">
      <img alt="not installed" src="./images/icons/dotgrey.gif" width="12" height="12" />
      {{if $can->edit}}
        <a class="action" href="{{$cmd}}=install">
          {{tr}}install{{/tr}}
        </a>
      {{/if}}
    </td>
  </tr>

  {{else}}
  {{assign var=module_id value=$mbmodule->_id}}
  {{assign var=cmd value="?m=system&a=domodsql&mod_id=$module_id&cmd"}}
  <tr>
    <td>
      <img alt="updown" src="./images/icons/updown.gif" width="10" height="15" border=0 usemap="#map-{{$mbmodule->_id}}" />
      {{if $can->edit}}
      <map name="map-{{$mbmodule->_id}}">
        <area coords="0,0,10,7"  href="{{$cmd}}=moveup" />
        <area coords="0,8,10,14" href="{{$cmd}}=movedn" />
      </map>
      {{/if}}
    </td>
    
    <td><strong>{{$mbmodule->mod_name}}</strong></td>

    <td>
    	<label title="{{tr}}module-{{$mbmodule->mod_name}}-long{{/tr}}">
    	  {{tr}}module-{{$mbmodule->mod_name}}-court{{/tr}}
    	</label>
    </td>

    <td>{{$mbmodule->mod_type}}</td>

    <td>
    	<!-- Actif -->
      {{mb_ternary var=dot test=$mbmodule->mod_active value=dotgreen other=dotyellowanim}}
      <img alt="dot" src="./images/icons/{{$dot}}.gif" />
      {{if $mbmodule->mod_type == "core"}}
      <strong>{{tr}}CModule-mod_active-{{$mbmodule->mod_active}}{{/tr}}</strong>
      {{else}}
      <a class="action" {{if $can->edit}}href="{{$cmd}}=toggle"{{/if}}>
	      {{tr}}CModule-mod_active-{{$mbmodule->mod_active}}{{/tr}}
      </a>
      {{/if}}

      <!-- Suppression -->
      {{if $mbmodule->mod_type != "core" && $can->edit}}
      |
      <a class="action"  href="{{$cmd}}=remove" onclick="return confirm('{{tr}}CModule-confirm-deletion{{/tr}}');">
        {{tr}}Remove{{/tr}}
      </a>
      {{/if}}
      
      {{if $mbmodule->_configable}}
      |
      <a class="action" href="{{$cmd}}=configure">
        {{tr}}configure{{/tr}}
      </a>
      {{/if}}
    </td>

    <!-- Mise à jour -->
    <td style="text-align: center;">
      {{if $mbmodule->_upgradable}}
      <a class="action" href="{{$cmd}}=upgrade" onclick="return confirm('{{tr}}CModule-confirm-upgrade{{/tr}}')">
        {{tr}}Upgrade{{/tr}} &gt; {{$mbmodule->_latest}}
      </a>
      {{else}}
      &mdash;
      {{/if}}
    </td>
    
    <td>
    	{{$mbmodule->mod_version}}
    </td>

    <td style="text-align: center;">
      {{mb_ternary var=dot test=$mbmodule->mod_ui_active value=dotgreen.gif other=dotredanim.gif}}
      <img alt="dot" src="./images/icons/{{$dot}}" />
      {{if $can->edit}}
      <a class="action" href="{{$cmd}}=toggleMenu">
      {{/if}}
      {{mb_value object=$mbmodule field=mod_ui_active}}
      {{if $can->edit}}
      </a>
      {{/if}}
    </td>
    <td>{{$mbmodule->mod_ui_order}}</td>
  </tr>
	{{/if}}
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