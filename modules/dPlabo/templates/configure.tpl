{{* $Id: $ *}}

<script type="text/javascript">

var Action = {
  module: "dPlabo",
  
  update: function (sName) {
    var url = new Url;
    url.setModuleAction(this.module, this.Requests[sName]);
    url.requestUpdate("action-" + sName);
  },
  
  Requests: {
    "import": "httpreq_import_catalogue"
  }
}

</script>

<h2>Environnement d'execution</h2>

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
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>

<table class="tbl">

<tr>
  <th class="title" colspan="100">Configuration</th>
</tr>

<tr>
  <td><button class="tick" onclick="Action.update('import')">Importer</button></td>
  <td id="action-import"></td>
</tr>


<tr>
  <th colspan="8" class="title">Praticiens du laboratoire</th>
</tr>
<tr>
  <th colspan="4">Praticien</th>
  <th colspan="4">ID Santé400</th>
</tr>
  {{foreach from=$list_idSante400 item=_idSante400}}
  <tr>
    <td colspan="4">
      {{assign var="object" value=$_idSante400->_ref_object}}
      <div onmouseover="ObjectTooltip.create(this, '{{$_idSante400->object_class}}', {{$_idSante400->object_id}})">
        {{$object->_view}}
      </div>
    </td>
    <td colspan="4">{{$_idSante400->id400}}</td>
  </tr>
  {{/foreach}}
</table>


  
<form name="editId400" action="" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPsante400" />
  <input type="hidden" name="dosql" value="do_idsante400_aed" />
  <input type="hidden" name="object_class" value="CMediusers" />
  <input type="hidden" name="tag" value="{{$remote_name}}" />
  <input type="hidden" name="last_update" value="{{$date}}" />

  <table class="tbl">
    <tr>
      <th colspan="8">Ajout d'un praticien au laboratoire {{$remote_name}}</th>
    </tr>
    <tr>
      <td>Praticien</td>
      <td>
	    <select name="object_id">
	    {{foreach from=$listPrat item=_prat}}
	      <option value="{{$_prat->_id}}">{{$_prat->_view}}</option>
	    {{/foreach}}
	    </select>
      </td>
 
      <td>{{mb_label object=$newId400 field="id400" }}</td>
      <td>{{mb_field object=$newId400 field="id400" canNull="false"}}</td>
 
      <td>
        <button class="submit" type="submit" name="btnFuseAction">Créer</button>
      </td>
    </tr>
  </table>
</form>