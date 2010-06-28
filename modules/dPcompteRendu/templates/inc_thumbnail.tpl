<script type="text/javascript">
  Thumb.nb_thumbs = {{$nbpages}};
  Thumb.file_id = {{$file_id}};
</script>

<div id="mess" style="position: fixed; width: 160px; font-size: 12pt; font-weight: bold; display: none; cursor: pointer;">
  <br/><br/>Vignettes obsolètes : cliquez sur une vignette pour réactualiser.<br/>
</div>

<!--<button type="button" class="hsplit" onclick="var url = new Url('dPcompteRendu', 'compare'); url.addParam('dialog', 1); url.addParam('file_id', Thumb.file_id); url.popup(1500,1100);">
  Comparer
</button>-->

{{assign var=i value=0}}
{{foreach from=$vignettes item=_vignette}}
  <p style="margin-bottom: 10px;">
	  <a id="thumb_{{$i}}" class="thumb" href="#1" onclick="return false">
	    <img class="thumbnail" src="data:image/jpg;base64,{{$_vignette}}" style="margin-bottom: 0px;" />
	  </a>
		<br/>
		{{$i+1}} / {{$nbpages}}
	</p>
	{{assign var=i value=$i+1}}
{{/foreach}}

<div id="toto" style="display: none;"></div>