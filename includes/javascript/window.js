window.getInnerDimensions = function() {
  var viewH = document.documentElement.clientHeight;
  var viewW = document.documentElement.clientWidth;
  return {x:viewH,y:viewW}
}