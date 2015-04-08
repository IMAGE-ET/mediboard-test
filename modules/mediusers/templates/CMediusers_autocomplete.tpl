{{* Do not add carriage returns or it will add whitespace in the input *}}
<div style="border-left: 3px solid #{{$match->_ref_function->color}}; padding-left: 2px; margin: -1px;">
	<span class="view" {{if $match->actif == 0}}style="text-decoration: line-through;"{{/if}}>{{if $show_view || !$f}}{{$match}}{{else}}{{$match->$f|emphasize:$input}}{{/if}}</span>
	<div style="text-align: right; color: #999; font-size: 0.8em;">{{$match->_ref_function}}</div>
</div>