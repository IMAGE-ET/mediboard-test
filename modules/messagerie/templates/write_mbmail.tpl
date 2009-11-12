{{if $dialog}}
{{assign var=destform value="m=$m&dialog=1&a=$action"}}
{{else}}
{{assign var=destform value="m=$m&tab=$tab"}}
{{/if}}

<form name="EditMbMail" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_mbmail_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="mbmail_id" value="{{$mbmail->_id}}" />
<input type="hidden" name="postRedirect" value="{{$destform}}" />

{{if !$mbmail->date_sent}}
{{mb_field object=$mbmail field=date_sent hidden=true}}
{{else}}
{{mb_field object=$mbmail field=archived hidden=true}}
{{mb_field object=$mbmail field=starred hidden=true}}
{{/if}}

<table class="form">
  <tr>
    <th colspan="2" class="category">
    	{{if $mbmail->_id}}
	      {{tr}}CMbMail-title-modify{{/tr}} '{{$mbmail}}'
	    {{else}}
	      {{tr}}CMbMail-title-create{{/tr}}
      {{/if}}
    </th>
  </tr>
  
  <tr>
	  <th>{{mb_label object=$mbmail field=from}}</th>
	  <td>
	  	{{mb_field object=$mbmail field=from hidden=1}}
	  	<div class="mediuser" style="border-color: #{{$mbmail->_ref_user_from->_ref_function->color}};">
	  		{{$mbmail->_ref_user_from}}
	  	</div>
	 	</td>
	</tr>

	{{if $mbmail->date_sent}}
  <tr>
	  <th>{{mb_label object=$mbmail field=date_sent}}</th>
	  <td>{{mb_value object=$mbmail field=date_sent}} ({{mb_value object=$mbmail field=date_sent format=relative}})</td>
	</tr>
	{{/if}}

  <tr>
	  <th>{{mb_label object=$mbmail field=to}}</th>
	  <td>
	    {{if $mbmail->date_sent}}
	  	<div class="mediuser" style="border-color: #{{$mbmail->_ref_user_to->_ref_function->color}};">
	  		{{$mbmail->_ref_user_to}}
	  	</div>
			{{else}}
      <select name="to" class="{{$mbmail->_props.to}} select-tree">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$functions item=_function}}
        <optgroup label="{{$_function->_view}} ({{$_function->_ref_users|@count}})">
        {{foreach from=$_function->_ref_users item=_user}}
          <option class="mediuser" style="border-color: #{{$_function->color}};" value="{{$_user->_id}}" 
            {{if $mbmail->to == $_user->_id}} selected="selected"{{/if}}>
            {{$_user}}
          </option>
        {{/foreach}}
        </optgroup>
        {{/foreach}}
      </select>
			{{/if}}
	  </td>
	</tr>

	{{if $mbmail->date_read}}
  <tr>
	  <th>{{mb_label object=$mbmail field=date_read}}</th>
	  <td>{{mb_value object=$mbmail field=date_read}} ({{mb_value object=$mbmail field=date_read format=relative}})</td>
	</tr>
	{{/if}}

	{{if $mbmail->archived}}
  <tr>
	  <th>{{mb_label object=$mbmail field=archived}}</th>
	  <td><strong>{{mb_value object=$mbmail field=archived}}</strong></td>
	</tr>
	{{/if}}

	{{if $mbmail->starred}}
  <tr>
	  <th>{{mb_label object=$mbmail field=starred}}</th>
	  <td><strong>{{mb_value object=$mbmail field=starred}}</strong></td>
	</tr>
	{{/if}}

  <tr>
	  <th>{{mb_label object=$mbmail field=subject}}</th>
	  <td>
	  	{{if $mbmail->date_sent}}
		  	{{mb_value object=$mbmail field=subject}}
		  {{else}}
		  	{{mb_field object=$mbmail field=subject}}
		  {{/if}}
	  </td>
	</tr>

  <tr>
	  <th>{{mb_label object=$mbmail field=source}}</th>
	  <td style="height: 300px">{{mb_field object=$mbmail field=source id="htmlarea"}}</td>	  
	</tr>

	{{if !$mbmail->date_sent}}
	<tr>
	  <td colspan="2" style="text-align: center;">
	    <button type="submit" class="send" onclick="$V(this.form.date_sent, 'now');">{{tr}}Send{{/tr}}</button>
	    <button type="submit" class="submit">{{tr}}Save{{/tr}} {{tr}}Draft{{/tr}}</button>
	  </td>
	</tr>
	{{elseif $mbmail->to == $app->user_id}}
	<tr>
	  <td colspan="2" style="text-align: center;">
	  	{{if !$mbmail->starred}}
		  	{{if $mbmail->archived}}
		    <button type="submit" class="cancel" onclick="$V(this.form.archived, '0');">{{tr}}Unarchive{{/tr}}</button>
				{{else}}
		    <button type="submit" class="change" onclick="$V(this.form.archived, '1');">{{tr}}Archive{{/tr}}</button>
				{{/if}}
			{{/if}}
						
	  	{{if !$mbmail->archived}}
		  	{{if $mbmail->starred}}
		    <button type="submit" class="cancel" onclick="$V(this.form.starred, '0');">{{tr}}Unstar{{/tr}}</button>
				{{else}}
		    <button type="submit" class="new" onclick="$V(this.form.starred, '1');">{{tr}}Star{{/tr}}</button>
				{{/if}}
			{{/if}}
	  </td>
	</tr>
	{{/if}}

</table>

</form>
