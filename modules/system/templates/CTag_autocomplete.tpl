{{* Do not add carriage returns or it will add whitespace in the input *}}
<span style="background: #{{$match->color}}; margin: -2px; margin-right: 1px; padding: 2px;">&nbsp;</span>
<span class="view" style="padding-left: {{$match->_deepness}}em;">{{$match->name}}</span>