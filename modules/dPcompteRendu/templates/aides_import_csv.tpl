<h2>Import des aides à la saisie pour {{$owner}}.</h2>

<form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1&amp;owner_guid={{$owner_guid}}" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  
  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import" />
  
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{$app->getMsg()|smarty:nodefaults}}