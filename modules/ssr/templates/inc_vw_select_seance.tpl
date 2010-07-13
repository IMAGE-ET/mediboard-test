<select name="seance_collective_id" style="display: none;" onchange="onchangeSeance(this.value);">
  <option value="">Nouvelle séance collective</option>
  {{foreach from=$seances item=_seance}}
	  <option value="{{$_seance->_id}}">{{mb_value object=$_seance field=debut}} - {{mb_value object=$_seance field=duree}} min</option>
	{{/foreach}}
</select> 