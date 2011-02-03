<script type="text/javascript">
  Thumb.nb_thumbs = {{$_nb_pages}};
  Thumb.file_id = {{$file_id}};
	// @FIXME: Pourquoi rafraichir la widget ici ???
  Main.add(function() {
  if(Thumb.compte_rendu_id) {
    try {
      window.opener.Document.refreshList(Thumb.object_class,Thumb.object_id);
     }
    catch (e) {}
  }
  });
</script>

{{$_nb_pages}} page{{if $_nb_pages > 1}}s{{/if}}
{{foreach from=1|range:$_nb_pages item=index}}
  <p style="margin-bottom: 10px;">
    <!--  Firefox refuse le min-width et min-height pour une image avec un src vide,  -->
      <img id="thumb_{{$index}}" class="thumb_empty thumbnail"
        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP4zwAAAgEBAKEeXHUAAAAASUVORK5CYII="
        style="margin-bottom: 0px; min-width: 138px; max-width: 138px; min-height: 195px;max-height: 195px; color: blank; cursor: pointer;"
        onclick="return false;"/>
  	<br/>
    {{$index}}
  </p>
{{/foreach}}
