{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=hl7v2_versions value="CHL7v2"|static:versions}} 

<script type="text/javascript">
  function extractSpecsFiles(version) {
    var url = new Url("hl7", "ajax_specs_hl7v2_files");
    url.addParam("extract", 1);
    url.addParam("version", version);
    url.requestUpdate('status_'+version);
  }
  
  function checkSpecsFiles(version) {
    var url = new Url("hl7", "ajax_specs_hl7v2_files");
    url.addParam("check", 1);
    url.addParam("version", version);
    url.requestUpdate('status_'+version);
  }
  
  Main.add( function(){
	  {{foreach from=$hl7v2_versions item=_hl7v2_version}}
      checkSpecsFiles('{{$_hl7v2_version}}');
    {{/foreach}}
  });
</script>
<table class="tbl">
  <tr>
    <th class="category">{{tr}}Version{{/tr}}</th>
    <th class="category">{{tr}}Action{{/tr}}</th>
    <th class="category">{{tr}}Status{{/tr}}</th>
  </tr>

  {{foreach from=$hl7v2_versions item=_hl7v2_version}}  
  <tr>
    <td style="width:1%">
      {{tr}}HL7-v.2.{{$_hl7v2_version}}{{/tr}}
    </td>
    
    <td onclick="extractSpecsFiles('{{$_hl7v2_version}}');" style="width:1%">
      <button class="tick">{{tr}}Install{{/tr}}</button>
      <div class="text" id="install_{{$_hl7v2_version}}"></div>
    </td>
    
    <td id="status_{{$_hl7v2_version}}">
      
    </td>
  </tr>
  {{/foreach}}
</table>