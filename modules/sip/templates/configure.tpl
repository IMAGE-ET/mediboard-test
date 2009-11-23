{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  
Main.add(function () {
  var tabs = Control.Tabs.create('tabs-transport');
  tabs.setActiveTab($V(getForm('editConfig')["sip[transport_layer]"]));
});
</script>

<h1>Configuration du module {{tr}}{{$m}}{{/tr}}</h1>
<hr />

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{mb_include module=system template=configure_handler class_handler=CSipObjectHandler}}
    
    {{assign var="mod" value="sip"}}
    <tr>
      <th class="category" colspan="10">{{tr}}config-{{$mod}}{{/tr}}</th>
    </tr>
        
    {{mb_include module=system template=inc_config_bool var=server}}
    
    {{mb_include module=system template=inc_config_bool var=send_all_patients}}
    
    {{assign var="var" value="transport_layer"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>    
      </th>
      <td class="text">
        <select class="str" name="{{$m}}[{{$var}}]">
          <option value="">&mdash; Couches &mdash;</option>
          <option value="ftp" {{if $dPconfig.$m.$var == "ftp"}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-ftp{{/tr}}</option>
          <option value="soap" {{if $dPconfig.$m.$var == "soap"}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-soap{{/tr}}</option>
        </select> 
      </td>            
    </tr>    
    
    <tr>
      <th class="category" colspan="10">{{tr}}config-{{$mod}}-transport{{/tr}}</th>
    </tr>
    <tr>
        <td colspan="2">
          <ul id="tabs-transport" class="control_tabs_vertical">
            <li><a href="#ftp">{{tr}}config-{{$m}}-{{$var}}-ftp{{/tr}}</a></li>
            <li><a href="#soap">{{tr}}config-{{$m}}-{{$var}}-soap{{/tr}}</a></li>
          </ul>
            
          <div id="ftp" style="display: none;">
            {{mb_include module=system template=configure_ftp ftpsn=SIP}}
            
            <table width="100%">
              
              
              {{assign var="var" value="fileprefix"}}
              <tr>
                <th>
                  <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
                    {{tr}}config-{{$m}}-{{$var}}{{/tr}}
                  </label>  
                </th>
                <td>
                  <input type="text" class="str" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
                </td>
              </tr>
              
              {{assign var="var" value="fileextension"}}
              <tr>
                <th>
                  <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
                    {{tr}}config-{{$m}}-{{$var}}{{/tr}}
                  </label>  
                </th>
                <td>
                  <input type="text" class="str" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
                </td>
              </tr>
              
              {{assign var="var" value="filenbroll"}}
              <tr>
                <th>
                  <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
                    {{tr}}config-{{$m}}-{{$var}}{{/tr}}
                  </label>  
                </th>
                <td>
                  <select name="{{$m}}[{{$var}}]">
                    <option value="1" {{if $dPconfig.$m.$var == 1}}selected="selected"{{/if}}>1</option>
                    <option value="2" {{if $dPconfig.$m.$var == 2}}selected="selected"{{/if}}>2</option>
                    <option value="3" {{if $dPconfig.$m.$var == 3}}selected="selected"{{/if}}>3</option>
                    <option value="4" {{if $dPconfig.$m.$var == 4}}selected="selected"{{/if}}>4</option>
                  </select>
                </td>
              </tr> 
            </table>
          </div>
          
          <div id="soap" style="display: none;">
            <table width="100%">
              {{mb_include module=system template=inc_config_bool var=wsdl_mode}}
            </table>
          </div>
        </td>
    </tr>

    <tr>
      <th class="category" colspan="10">{{tr}}config-{{$mod}}-export{{/tr}}</th>
    </tr>
    <tr>
	    {{assign var="var" value="export_segment"}}
	    <th>
	      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
	      </label>  
	    </th>
	    <td>
	      <input class="num" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
	    </td>
	  </tr>
	  
	  <tr>
	    {{assign var="var" value="export_id_min"}}
	    <th>
	      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
	      </label>  
	    </th>
	    <td>
	      <input class="numchar" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
	    </td>
	  </tr>
	
	  <tr>
	    {{assign var="var" value="export_id_max"}}
	    <th>
	      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
	        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
	      </label>  
	    </th>
	    <td>
	      <input class="numchar" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
	    </td>
	  </tr>
	  
	  <tr>
      {{assign var="var" value="batch_count"}}
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <input class="numchar" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
      </td>
    </tr>
  	
  	{{mb_include module=system template=inc_config_bool var=pat_no_ipp}}
    
    {{mb_include module=system template=inc_config_bool var=sej_no_numdos}}
  	  		    
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<hr />

<!-- Import des tables -->
<script type="text/javascript">

var Action = {
  module: "sip",
  
  import: function (sAction, type) {
    var url = new Url;
    url.setModuleAction(this.module, "ajax_export_"+type);
    url.addParam("action", sAction);
    url.requestUpdate("export-"+type);
  },
}

</script>

<table class="tbl">

<tr>
  <th class="title" colspan="100">{{tr}}sip-export-classes{{/tr}}</th>
</tr>

<tr>
  <td>
    {{tr}}sip-export-class{{/tr}} '{{tr}}CPatient{{/tr}}'
  </td>
  <td>
    <button type="button" class="new" onclick="Action.import('start', 'patient')">
      {{tr}}Start{{/tr}}      
    </button>
    <button type="button" class="change" onclick="Action.import('retry', 'patient')">
      {{tr}}Retry{{/tr}}      
    </button>
    <button type="button" class="tick" onclick="Action.import('continue', 'patient')">
      {{tr}}Continue{{/tr}}      
    </button>
  </td>
  <td id="export-patient"></td>
</tr>

<tr>
  <td>
    {{tr}}sip-export-class{{/tr}} '{{tr}}CSejour{{/tr}}'
  </td>
  <td>
    <button type="button" class="new" onclick="Action.import('start', 'sejour')">
      {{tr}}Start{{/tr}}      
    </button>
    <button type="button" class="change" onclick="Action.import('retry', 'sejour')">
      {{tr}}Retry{{/tr}}      
    </button>
    <button type="button" class="tick" onclick="Action.import('continue', 'sejour')">
      {{tr}}Continue{{/tr}}      
    </button>
  </td>
  <td id="export-sejour"></td>
</tr>

</table>