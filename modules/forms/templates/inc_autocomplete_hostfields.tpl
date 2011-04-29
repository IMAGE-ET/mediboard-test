<ul>
{{* 
{{foreach from=$ex_class->_host_class_fields key=_field item=_spec}}
  <li data-spec="{{$_spec}}" data-value="{{$_field}}">
    {{assign var=_subfield value="."|explode:$_field}}
    
    <small style="float: right; color: #666;">
      {{if $_spec instanceof CRefSpec && $_spec->class}}
        {{if $_spec->meta}}
          {{assign var=_meta value=$_spec->meta}}
          {{assign var=_meta_spec value=$ex_class->_host_class_fields.$_meta}}
          {{" OU "|@implode:$_meta_spec->_locales}}
        {{else}}
          {{tr}}{{$_spec->class}}{{/tr}}
        {{/if}}
      {{else}}
        {{tr}}CMbFieldSpec.type.{{$_spec->getSpecType()}}{{/tr}}
      {{/if}}
		</small>
    
    <strong class="view">
    	{{if $_subfield|@count > 1}}
	      {{tr}}{{$ex_class->host_class}}-{{$_subfield.0}}{{/tr}} de type {{tr}}{{$_subfield.1}}{{/tr}}
	    {{else}}
	      {{tr}}{{$ex_class->host_class}}-{{$_field}}{{/tr}} 
	    {{/if}}
		</strong>
  </li>
  
  {{if $_spec instanceof CRefSpec}}
    {{foreach from=$_spec->_subspecs key=_key item=_subspec}}
      {{assign var=_subfield value="."|explode:$_key}}
      {{assign var=_subfield value=$_subfield.0}}
      
      <li data-spec="{{$_subspec}}" data-value="{{$_field}}-{{$_key}}" >
        <small style="float: right; color: #666;">
          {{if $_subspec instanceof CRefSpec && $_subspec->class}}
            {{if $_subspec->meta}}
            {{else}}
              {{tr}}{{$_subspec->class}}{{/tr}}
            {{/if}}
          {{else}}
            {{tr}}CMbFieldSpec.type.{{$_subspec->getSpecType()}}{{/tr}}
          {{/if}}
        </small>
				
				<span class="view">
          &nbsp; |&ndash; {{tr}}{{$_subspec->className}}-{{$_subfield}}{{/tr}} 
				</span>
      </li>
    {{/foreach}}
  {{/if}}
{{/foreach}}
*}}

{{foreach from=$host_fields item=element key=value}}
  <li data-prop="{{$element.prop}}" data-value="{{$value}}">
  	<small style="float: right; color: #666;">
      {{$element.type}}
    </small>
    
    <span class="view" {{if !$show_views}} style="display: none;" {{/if}}>
      {{$element.view}}
    </span>
		
		<span style="{{if $show_views}} display: none; {{/if}} padding-left: {{$element.level}}em; {{if $element.level == 0}}font-weight: bold{{/if}}">
			{{$element.title}}
		</span>
  </li>
{{/foreach}}

</ul>