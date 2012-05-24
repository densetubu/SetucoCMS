// This list may be created by a server logic page PHP/ASP/ASPX/JSP in some backend system.
// There flash movies will be displayed as a dropdown in all media dialog if the "media_external_list_url"
// option is defined in TinyMCE init.

var mediaList = new ApiDataList("/api/media");
var tinyMCEMediaList = mediaList.getTinyMCEListDataByMapList(["name", "uploadUrl"]);