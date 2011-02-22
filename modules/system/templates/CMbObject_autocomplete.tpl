{{* Do not add carriage returns or it will add whitespace in the input *}}
<span class="view">{{if $show_view}}{{$match->_view}}{{else}}{{$match->$f|emphasize:$input}}{{/if}}</span>