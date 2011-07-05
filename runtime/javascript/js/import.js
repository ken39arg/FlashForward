function include(src) {
  document.write("<script type='text/javascript' src='js/src/"+src+"'><\/script>");
}

include("package.js");
include("core.js");
include("event.js");
include("player.js");
include("context.js");
include("avm.js");
include("geom/rect.js");
include("geom/matrix.js");
include("geom/cxform.js");
include("display/display.js");
include("display/bitmap.js");
include("display/shape.js");
include("display/font.js");
include("display/text.js");
include("display/display_container.js");
include("display/sprite.js");
include("display/stage.js");
include("renderer/svg.js");
include("renderer/canvas.js");

