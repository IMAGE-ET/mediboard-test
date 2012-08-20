<span class="view">{{$match->_view}}</span>
<div style="color: #999; font-size: 0.9em;">
  {{mb_value object=$match field=adresse}}
  {{mb_value object=$match field=cp}}
  {{mb_value object=$match field=ville}}
</div>
<div style="color: #999; font-size: 0.9em; float: right;">{{mb_value object=$match field=naissance}}</div>
<div style="color: #999; font-size: 0.9em; float: left;">{{mb_value object=$match field=tel}}</div>