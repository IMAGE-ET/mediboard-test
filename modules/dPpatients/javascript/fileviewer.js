/**
 * Created by flavien on 16/03/15.
 */

expandDocDisplay = window.expandDocDisplay || function(figure, show) {
  var toolbar = figure.down(".toolbar");
  if (show) {
    toolbar.setStyle({visibility: "visible"});
  }
  else {
    toolbar.setStyle({visibility: "hidden"});
  }
};

popFile = window.popFile || function(objectClass, objectId, elementClass, elementId, sfn) {
  new Url().ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
};