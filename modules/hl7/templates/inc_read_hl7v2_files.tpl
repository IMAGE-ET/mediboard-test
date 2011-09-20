<script>
  Main.add(function(){
    Control.Tabs.create("messages-tab");
  });
</script>

<table class="main layout">
  <tr>
    <td style="vertical-align: top; white-space: nowrap;" class="narrow">
      <ul id="messages-tab" class="control_tabs_vertical small" style="min-width: 10em;">
        {{foreach from=$messages item=_message key=_key}}
          <li>
            <a href="#message-{{$_key}}" {{if $_message->errors}} class="wrong" {{/if}} title="{{$_message->filename}}">
              <strong style="float: left; margin-right: 1em;">{{$_message->name}}</strong> {{$_message->version}}
            </a>
          </li>
        {{/foreach}}
      </ul>
    </td>

    <td class="text" style="padding: 3px;">
      {{foreach from=$messages item=_message key=_key}}
        <script>
          Main.add(function(){
            Control.Tabs.create("message-tab-{{$_key}}");
						var tree = new TreeView("message-{{$_key}}-tree");
						tree.collapseAll();
          });
        </script>
        <div style="display: none;" id="message-{{$_key}}">
          <h1>{{$_message->description}} ({{$_message->version}}) <span class="type">{{$_message->name}}</span></h1>
          
          <ul class="control_tabs" id="message-tab-{{$_key}}">
            <li><a href="#message-{{$_key}}-tree">Arbre</a></li>
            <li><a href="#message-{{$_key}}-er7-input">ER7 Input</a></li>
            <li><a href="#message-{{$_key}}-er7-output">ER7 Output</a></li>
            <li><a href="#message-{{$_key}}-warnings" {{if $_message->errors|@count}} class="wrong" {{/if}}>Avertissements</a></li>
            <li><a href="#message-{{$_key}}-errors" {{if $_message->errors|@count}} class="wrong" {{/if}}>Erreurs</a></li>
          </ul>
          <hr class="control_tabs" />
           
					<div id="message-{{$_key}}-tree" style="display: none;">
	          <ul class="hl7-tree">
	            {{mb_include module=hl7 template=inc_segment_group_children segment_group=$_message}}
	          </ul>
					</div>
          
          <div id="message-{{$_key}}-er7-input" style="display: none;">
            {{$_message->highlight_er7($_message->data)|smarty:nodefaults}}
          </div>
          
          <div id="message-{{$_key}}-er7-output" style="display: none;">
            {{$_message->flatten(true)|smarty:nodefaults}}
          </div>
          
          <div id="message-{{$_key}}-warnings" style="display: none;">
            {{mb_include module=hl7 template=inc_hl7v2_errors errors=$_message->errors level=1}}
          </div>
          
          <div id="message-{{$_key}}-errors" style="display: none;">
            {{mb_include module=hl7 template=inc_hl7v2_errors errors=$_message->errors level=2}}
          </div>
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>