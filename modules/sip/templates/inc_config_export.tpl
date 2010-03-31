{{* $Id: configure.tpl 8207 2010-03-04 17:05:05Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 8207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfigExport" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
  	{{assign var="mod" value="sip"}}
    <tr>
      <th class="title" colspan="10">{{tr}}config-{{$mod}}-export{{/tr}}</th>
    </tr>
    {{mb_include module=system template=inc_config_str var=export_segment}}
	  
    {{mb_include module=system template=inc_config_str var=export_id_min}}
    
    {{mb_include module=system template=inc_config_str var=export_id_max}}
    
    {{mb_include module=system template=inc_config_str var=export_date_min}}
    
    {{mb_include module=system template=inc_config_str var=export_date_max}}
	  
    {{mb_include module=system template=inc_config_str var=batch_count}}
	  
  	{{mb_include module=system template=inc_config_bool var=pat_no_ipp}}
    
    {{mb_include module=system template=inc_config_bool var=sej_no_numdos}}
    
    {{mb_include module=system template=inc_config_bool var=send_sej_pa}}
  	  		    
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

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