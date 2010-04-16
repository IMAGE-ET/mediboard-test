{{assign var=i value=0}}
{{foreach from=$vignettes item=_vignette}}
  <p style="margin-bottom: 10px;">
	  <a id="thumb_{{$i}}" href="#1" onclick="(new Url).ViewFilePopup('CCompteRendu',{{$compte_rendu_id}},'CFile','{{$file_id}}', {{$i}}); return false;">
	    <img class="thumbnail" src="data:image/jpg;base64,{{$_vignette}}" style="margin-bottom: 0px;"/>
	  </a>
		<br/>
		{{$i+1}} / {{$nbpages}}
	</p>
	{{assign var=i value=$i+1}}
{{/foreach}}