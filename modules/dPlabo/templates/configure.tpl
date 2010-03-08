{{* $Id$ *}}

<script type="text/javascript">

var Action = {
  module: "dPlabo",
  
  update: function (sName) {
    var url = new Url;
    url.setModuleAction(this.module, this.Requests[sName]);
    url.requestUpdate("action-" + sName);
  },
  
  Requests: {
    "importCatalogues": "httpreq_import_catalogue",
    "importPacks": "httpreq_import_pack"
  }
}

</script>

<h2>Environnement d'execution</h2>

<table class="form">  
  <tr>
    <th class="category">
      {{tr}}config-exchange-source{{/tr}} '{{$prescriptionlabo_source->name}}'
    </th>
  </tr>
  <tr>
    <td> {{mb_include module=system template=inc_config_exchange_source source=$prescriptionlabo_source}} </td>
  </tr>
</table>

<table class="form">  
  <tr>
    <th class="category">
      {{tr}}config-exchange-source{{/tr}} '{{$get_id_prescriptionlabo_source->name}}'
    </th>
  </tr>
  <tr>
    <td> {{mb_include module=system template=inc_config_exchange_source source=$get_id_prescriptionlabo_source}} </td>
  </tr>
</table>
  
<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <!-- CCatalogueLabo -->  
  {{assign var="class" value="CCatalogueLabo"}}
    
  <tr>
    <th class="category" colspan="100">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="remote_name"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="notNull str maxLength|8" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>  
    
  {{assign var="var" value="remote_url"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="notNull url" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>

  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>

<table class="tbl">

<tr>
  <th class="title" colspan="100">Configuration</th>
</tr>

<tr>
  <td><button class="tick" onclick="Action.update('importCatalogues')">Importer</button></td>
  <td id="action-importCatalogues"></td>
</tr>

</table>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <!-- CPack -->  
  {{assign var="class" value="CPackExamensLabo"}}
    
  <tr>
    <th class="category" colspan="100">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
    
  {{assign var="var" value="remote_url"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="notNull url" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>  
    
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>

<table class="tbl">

<tr>
  <th class="title" colspan="100">Configuration</th>
</tr>

<tr>
  <td><button class="tick" onclick="Action.update('importPacks')">Importer</button></td>
  <td id="action-importPacks"></td>
</tr>

</table>