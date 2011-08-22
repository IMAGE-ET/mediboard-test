
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

Main.add(function(){
  prepareEmptyRows();
  toggleEmptyRows();
  
  if ($("exclass_tabs")) {
    Control.Tabs.create("exclass_tabs");
  }
});

prepareEmptyRows = function(){
  var container = $("ex_class-tables");
  if (!container) return;
  
  var emptyBodies = container.select(".ex_class-table tbody").filter(function(tbody){ 
    var emptyRows = tbody.select('tr.field.empty').length;
    var allRows = tbody.select('tr.field').length;
    var empty = (emptyRows == allRows);
    
    if (!empty) {
      var th = tbody.down('th');
      th.addClassName("rowspan-changed");
      th.emptyRowSpan = allRows-emptyRows+1;
      th.origRowSpan = th.rowSpan;
    }
    
    return empty;
  });
  
  emptyBodies.invoke("addClassName", "empty");
}

toggleEmptyRows = function(){
  var container = $("ex_class-tables");
  if (!container) return;
  
  var show = !container.hasClassName("hide-empty-rows");
  
  container.select(".ex_class-table .empty").invoke("setVisible", show);
  
  container.select(".ex_class-table th.rowspan-changed").each(function(th) {
    th.rowSpan = (show ? th.origRowSpan : th.emptyRowSpan);
  });
  
  container.toggleClassName("hide-empty-rows", show);
}

refreshSelf = function(){
  ExObject.loadExObjects('{{$reference_class}}', '{{$reference_id}}', '{{$target_element}}', '{{$detail}}', '{{$ex_class_id}}');
}

</script>

{{* FULL DETAIL *}}
{{if $detail == 2}}

  {{if $ex_objects_by_event|@count && !$ex_class_id}}
    <ul class="control_tabs small" id="exclass_tabs">
      {{foreach from=$ex_objects_by_event item=ex_objects_by_class key=_host_event}}
        {{assign var=parts value="-"|explode:$_host_event}}
        <li>
          <a href="#tab-{{$_host_event}}">{{tr}}{{$parts.0}}{{/tr}} - {{tr}}{{$parts.0}}-event-{{$parts.1}}{{/tr}}</a>
        </li>
      {{/foreach}}
    </ul>
    
    <hr class="control_tabs" />
  {{/if}}
  
  <div id="ex_class-tables" class="hide-empty-rows">
    <button class="change" onclick="toggleEmptyRows()">
      Afficher/cacher les valeurs vides
    </button>
    
    {{foreach from=$ex_objects_by_event item=ex_objects_by_class key=_host_event}}
      <div id="tab-{{$_host_event}}" {{if !$ex_class_id}} style="display: none;" {{/if}} class="ex_class-table">
      	{{if $reaches_limit && $limit}}
      	  <div class="small-info">Seuls les {{$limit}} derniers enregistrements de chaque formulaire sont affich�s.</div>
				{{/if}}
        {{mb_include module=forms template=inc_ex_objects_columns}}
      </div>
    {{foreachelse}}
      <div class="empty">Aucun formulaire</div>
    {{/foreach}}
  </div>

{{* MEDIUM DETAIL *}}
{{elseif $detail == 1}}

  {{if $ex_objects_by_event|@count > 1}}
    <ul class="control_tabs small" id="exclass_tabs">
      {{foreach from=$ex_objects_by_event item=ex_objects_by_class key=_host_event}}
        {{assign var=parts value="-"|explode:$_host_event}}
        <li>
          <a href="#tab-{{$_host_event}}">
          	{{tr}}{{$parts.0}}{{/tr}} - {{tr}}{{$parts.0}}-event-{{$parts.1}}{{/tr}}
					</a>
        </li>
      {{/foreach}}
    </ul>
    
    <hr class="control_tabs" />
  {{/if}}
  
  {{foreach from=$ex_objects_by_event item=ex_objects_by_class key=_host_event}}
  <div id="tab-{{$_host_event}}" {{if $ex_objects_by_event|@count > 1}} style="display: none;" {{/if}}>
		
    {{if $reaches_limit && $limit}}
      <div class="small-info">Seuls les {{$limit}} derniers enregistrements de chaque formulaire sont affich�s.</div>
    {{/if}}
		
	  {{assign var=_parts value="-"|explode:$_host_event}}
	  {{if $_parts.0 == $reference_class && $ex_classes_creation.$_host_event|@count > 0}}
		  <table class="main layout">
		  	<tr>
		  		<td style="text-align: right;">Remplir un nouveau formulaire:</td>
					<td>
						<select onchange="selectExClass(this, '{{$reference_class}}-{{$reference_id}}', '{{$_parts.1}}', '@refreshSelf')">
	            <option>&ndash; Formulaires disponibles</option>
	            {{foreach from=$ex_classes_creation.$_host_event item=_ex_class key=_ex_class_id}}
	              <option value="{{$_ex_class->_id}}">{{$_ex_class->name}}</option>
	            {{/foreach}}
	          </select>
					</td>
		  	</tr>
		  </table>
			<hr style="border-color: #aaa;" />
		{{/if}}
		
    {{foreach from=$ex_objects_by_class item=_ex_objects key=_ex_class_id}}
      {{assign var=_ex_obj value=$_ex_objects|@reset}}
      {{assign var=_ex_class value=$ex_classes.$_ex_class_id}}
			
			{{if $_ex_objects|@count}}
      <h3 style="margin: 0.5em 1em;">
        {{if $_ex_class->host_class == $reference_class}}
          <button style="margin: -1px; float: right;" class="new" 
                  onclick="showExClassForm('{{$_ex_class_id}}', '{{$reference_class}}-{{$reference_id}}', '{{$_ex_class->host_class}}-{{$_ex_class->event}}', null, '{{$_ex_class->event}}', '@refreshSelf')">
            {{tr}}New{{/tr}}
          </button>
        {{/if}}
				
			  {{$_ex_class->name}}
      </h3>
      
      {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
         <table class="layout">
           <tr>
             <td>
               <button style="margin: -1px;" class="edit notext" 
                       onclick="ExObject.edit('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
                 {{tr}}Edit{{/tr}}
               </button>
               
               <button style="margin: -1px;" class="search notext" 
                       onclick="ExObject.display('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
                 {{tr}}Display{{/tr}}
               </button>
              
               <button style="margin: -1px;" class="history notext" 
                       onclick="ExObject.history('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}')">
                 {{tr}}History{{/tr}}
               </button>
               
               <button style="margin: -1px;" class="print notext" 
                       onclick="ExObject.print('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
                 {{tr}}Print{{/tr}}
               </button>
             </td>
             <td class="text compact">
               <strong style="color: #000;">{{mb_value object=$_ex_object->_ref_first_log field=date}}</strong>
							 
							 {{if $_ex_class->host_class != $reference_class}}
	               <br />
	               {{$_ex_object->_ref_object}}
							{{/if}}
             </td>
           </tr>
         </table>
      {{/foreach}}
			
      <hr style="border-color: #aaa;" />
			{{/if}}
			
    {{/foreach}}
    </div>
  {{foreachelse}}
    <div class="empty">Aucun formulaire</div>
  {{/foreach}}
  

{{* NO DETAIL *}}
{{else}}

{{if $ex_objects_by_event|@count > 0}}
<table class="main layout">
  <tr>
    <td class="narrow" style="min-width: 20em;">
      <table class="main tbl">
        {{foreach from=$ex_objects_by_event item=ex_objects_by_class key=_host_event}}
          <tr>
            <th colspan="2">
              {{assign var=parts value="-"|explode:$_host_event}}
              {{tr}}{{$parts.0}}{{/tr}} - {{tr}}{{$parts.0}}-event-{{$parts.1}}{{/tr}}
            </th>
          </tr>
				
          {{foreach from=$ex_objects_by_class item=_ex_objects key=_ex_class_id}}
						{{if $_ex_objects|@count}}
	          <tr>
	            <td class="text">
	              {{$ex_classes.$_ex_class_id->name}}
	            </td>
	            <td class="narrow" style="text-align: right;">
	              <button class="right rtl" style="margin: -1px" 
	                      onclick="ExObject.loadExObjects('{{$reference_class}}', '{{$reference_id}}', 'ex_class-list', 2, '{{$_ex_class_id}}')">
	                {{$_ex_objects|@count}}
	              </button>
	            </td>
	          </tr>
						{{/if}}
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>
    <td id="ex_class-list">
      <div class="small-info">
        Cliquez sur le bouton correspondant au formulaire dont vous voulez voir le d�tail
      </div>
    </td>
  </tr>
</table>
{{else}}
  <div class="small-info">
    Aucun formulaire saisi
  </div>
{{/if}}

{{/if}}