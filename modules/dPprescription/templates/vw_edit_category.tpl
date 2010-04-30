{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="mediusers" script="color_selector"}}

<script type="text/javascript">

ColorSelector.init = function(form_name, color_view){
  this.sForm  = form_name;
  this.sColor = "color";
	this.sColorView = color_view;
  this.pop();
}

Main.add( function(){
  categories_tab = new Control.Tabs.create('categories_tab', true);

  if($('code_auto_complete')){
	  var url = new Url("ssr", "httpreq_do_activite_autocomplete");
	  url.autoComplete("editCdarr_code", "code_auto_complete", {
	    minChars: 2,
	    select: ".value"
	  } );
	}	
});
	
</script>

<table class="main">
	<tr>
	  <td class="halfPane">
	  	{{mb_include template=inc_list_categories}}
      <hr />
  		{{if $category->_id}}
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_id=0" class="button new">
        Créer un élément
      </a>
      
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;mode_duplication=1" class="button new">
        Dupliquer des elements
      </a>
			<div id="element-list">
	      {{mb_include template=inc_list_elements}}
	    </div>
	    <script type="text/javascript">ViewPort.SetAvlHeight('element-list', 0.35);</script>
			{{/if}}

      {{if $element_prescription->_id && @$modules.ssr->mod_active}}
      <hr />
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_to_cdarr_id=0" class="button new">
        Ajouter un code CdARR
      </a>
      <div id="cdarr-list">
        {{mb_include template=inc_list_element_cdarrs}}
      </div>
      <script type="text/javascript">ViewPort.SetAvlHeight('cdarr-list', 0.4);</script>
      {{/if}}
	  </td>
		 
    <td class="halfPane">
      {{mb_include template=inc_form_category}}
			
			{{if $category->_id}}
      <hr />
			{{if $mode_duplication}}
        {{mb_include template=inc_form_elements_duplication}}
      {{else}}
        {{mb_include template=inc_form_element}}
				
			  {{if $element_prescription->_id && @$modules.ssr->mod_active}}
        <hr />
				{{mb_include template=inc_form_element_cdarr}}
    		{{/if}}
		  {{/if}} 
		{{/if}}
    </td>
  </tr>
</table>