// This list may be created by a server logic page PHP/ASP/ASPX/JSP in some backend system.
// There images will be displayed as a dropdown in all image dialogs if the "external_link_image_url"
// option is defined in TinyMCE init.

var mediaList = new ApiDataList("/api/media/image");
var tinyMCEImageList = mediaList.getTinyMCEListDataByMapList(["name", "uploadUrl"]);