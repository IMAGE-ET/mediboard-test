
{{*
<table class="main tbl vertical">
{{foreach from=$all_ex_objects item=_ex_object key=key}}
  <tr>
    {{foreach from=$_ex_object->_ref_ex_class->_ref_groups item=_ex_group}}
      {{foreach from=$_ex_group->_ref_fields item=_ex_field}}
        <th><span>{{$_ex_field}}</span></th>
      {{/foreach}}
    {{/foreach}}
  </tr>
  <tr>
  	{{foreach from=$_ex_object->_ref_ex_class->_ref_groups item=_ex_group}}
		  {{foreach from=$_ex_group->_ref_fields item=_ex_field}}
        <td>{{mb_value object=$_ex_object field=$_ex_field->name}}</td>
			{{/foreach}}
		{{/foreach}}
	</tr>
{{/foreach}}
*}}

<script type="text/javascript">
	
ExObject = {
  print: function(ex_object_id, ex_class_id, object_guid) {
	  var printIframe = $("printIframe");
	  printIframe.src = "about:blank";
	  printIframe.src = "?m=forms&a=view_ex_object_form&ex_object_id="+ex_object_id+"&ex_class_id="+ex_class_id+"&object_guid="+object_guid+"&dialog=1&readonly=1&print=1";
	},
	
	show: function(mode, ex_object_id, ex_class_id, object_guid){
    var url = new Url("forms", "view_ex_object_form");
    url.addParam("ex_object_id", ex_object_id);
    url.addParam("ex_class_id", ex_class_id);
    url.addParam("object_guid", object_guid);
		
		if (mode == "display") {
      url.addParam("readonly", 1);
		}
		
    url.pop("100%", "100%", mode+"-"+ex_object_id);
	},
	
	display: function(ex_object_id, ex_class_id, object_guid){
	  ExObject.show("display", ex_object_id, ex_class_id, object_guid);
	},
  
  edit: function(ex_object_id, ex_class_id, object_guid){
    ExObject.show("edit", ex_object_id, ex_class_id, object_guid);
  },
  
  history: function(ex_object_id, ex_class_id){
	  var url = new Url("system", "view_history");
	  url.addParam("object_class", "CExObject");
    url.addParam("object_id", ex_object_id);
    url.addParam("ex_class_id", ex_class_id);
	  url.addParam("user_id", "");
	  url.addParam("type", "");
	  url.popup(900, 600, "history");
  }
}

Main.add(function(){
  Control.Tabs.create("exclass_tabs");
});

</script>

<ul class="control_tabs" id="exclass_tabs">
{{foreach from=$ex_objects_by_event item=ex_objects_by_class key=_host_event}}
  {{assign var=parts value="-"|explode:$_host_event}}
  <li><a href="#tab-{{$_host_event}}">{{tr}}{{$parts.0}}{{/tr}} - {{$parts.1}}</a></li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{if $detail}}

{{foreach from=$ex_objects_by_event item=ex_objects_by_class key=_host_event}}
  <div id="tab-{{$_host_event}}" style="display: none;">
	{{foreach from=$ex_objects_by_class item=_ex_objects key=_ex_class_id}}
	  {{assign var=_ex_obj value=$_ex_objects|@reset}}
	
		<h2>{{$ex_classes.$_ex_class_id->name}}</h2>
		
	  <table class="main tbl vertical" style="width: 1%;">
		  <!-- First line -->
		  <tr>
	      <th class="narrow">
	      </th>
	      <th style="min-width: 20em;">Champ</th>
			  {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
				  <th class="narrow">
             
            {{mb_value object=$_ex_object->_ref_first_log field=date}}
             <hr />
						
		         <button style="margin: 0 -1px;" class="edit notext" 
		                 onclick="ExObject.edit('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
		           {{tr}}Edit{{/tr}}
		         </button>
		         
		         <button style="margin: 0 -1px;" class="search notext" 
		                 onclick="ExObject.display('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
		           {{tr}}Display{{/tr}}
		         </button>
		        
		         <button style="margin: 0 -1px;" class="history notext" 
		                 onclick="ExObject.history('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}')">
		           {{tr}}History{{/tr}}
		         </button>
		         
		         <button style="margin: 0 -1px;" class="print notext" 
		                 onclick="ExObject.print('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
		           {{tr}}Print{{/tr}}
		         </button>
					</th>
				{{/foreach}}
	    </tr>
			
		  {{foreach from=$_ex_obj->_ref_ex_class->_ref_groups item=_ex_group name=_ex_group}}
			  
        <tbody style="border-bottom: 2px solid black;">
				<tr>
					<th rowspan="{{$_ex_group->_ref_fields|@count}}">
						{{vertical}}{{$_ex_group}}{{/vertical}}
					</th>
					
	        {{foreach from=$_ex_group->_ref_fields item=_ex_field name=_ex_field}}
					  <td class="text" style="font-weight: bold;">
						  {{mb_label object=$_ex_obj field=$_ex_field->name}}</span>
						</td>
						
					  {{foreach from=$_ex_objects item=_ex_object2 name=_ex_object2}}
	            <td class="text">
	            	{{mb_value object=$_ex_object2 field=$_ex_field->name}}
							</td>
							
							{{if $smarty.foreach._ex_object2.last}}
							  </tr>
							{{/if}}
					  {{/foreach}}
						
            {{if $smarty.foreach._ex_field.last}}
              </tbody>
            {{/if}}
	        {{/foreach}}
			{{/foreach}}
	  </table>
	
	{{/foreach}}
  </div>
{{foreachelse}}
  <div class="empty">Aucun formulaire</div>
{{/foreach}}

{{else}}

{{foreach from=$ex_objects_by_event item=ex_objects_by_class key=_host_event}}
  <div id="tab-{{$_host_event}}" style="display: none;">
  {{foreach from=$ex_objects_by_class item=_ex_objects key=_ex_class_id}}
    {{assign var=_ex_obj value=$_ex_objects|@reset}}
  
    <h3>{{$ex_classes.$_ex_class_id->name}}</h3>
    
		<ul>
    {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
       <li>
         <button style="margin: 0 -1px;" class="edit notext" 
                 onclick="ExObject.edit('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
					 {{tr}}Edit{{/tr}}
				 </button>
				 
         <button style="margin: 0 -1px;" class="search notext" 
                 onclick="ExObject.display('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
					 {{tr}}Display{{/tr}}
				 </button>
				
         <button style="margin: 0 -1px;" class="history notext" 
                 onclick="ExObject.history('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}')">
           {{tr}}History{{/tr}}
				 </button>
				 
         <button style="margin: 0 -1px;" class="print notext" 
				         onclick="ExObject.print('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
				   {{tr}}Print{{/tr}}
				 </button>
				 
       	 &ndash; 
				 <strong>{{mb_value object=$_ex_object->_ref_first_log field=date}}</strong>
				 &ndash; 
				 {{$_ex_object->_ref_object}}
			 </li>
    {{/foreach}}
		</ul>
  
  {{/foreach}}
  </div>
{{foreachelse}}
  <div class="empty">Aucun formulaire</div>
{{/foreach}}

{{/if}}