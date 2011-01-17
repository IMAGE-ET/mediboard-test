<div id="{{$dir}}-header" class="tree-header" style="height: 22px;">
	<div id="{{$dir}}-trigger" class="tree-trigger">{{tr}}Toggle{{/tr}}</div>  
  <div class="sniffer" style="float: right;">
    {{if is_array($files)}}
      <button type="button" class="down notext" onclick="CodeSniffer.run(this);">{{tr}}Run{{/tr}}</button>
    {{else}}
      <button type="button" class="search notext" onclick="CodeSniffer.show(this);">{{tr}}Show{{/tr}}</button>
    {{/if}}
  </div>
  <input name="file-select" type="radio" onclick="CodeSniffer.select(this);"/>
	<span class="basename">
    {{$basename}}
	</span>
</div>
{{if is_array($files)}}
<div class="tree-content" id="{{$dir}}" style="display: block;">
  {{foreach from=$files key=_dir item=_files}}
  {{mb_include template=tree_sniffed_files basename=$_dir dir="$dir:$_dir" files=$_files}}
  {{/foreach}}
</div>
{{/if}}