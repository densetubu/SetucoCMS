// This list may be created by a server logic page PHP/ASP/ASPX/JSP in some backend system.
// There images will be displayed as a dropdown in all image dialogs if the "external_link_image_url"
// option is defined in TinyMCE init.
//
//var tinyMCEImageList = new Array(
//	// Name, URL
//	["Logo 1", "/js/tinymce_js/media/logo.jpg"],
//	["Logo 2", "/js/tinymce_js/media/logo.jpg"],
//);

//function jsonArray(){
//	var tinyMCEImageList = new Array();
//	
//	return function(name, uploadUrl){
//		
//		console.log(name);
//		
//		tinyMCEImageList.push([name,uploadUrl]);	
//		return tinyMCEImageList;
//	}
//}
//	
//var jsonRet = jsonArray();
//
//$.getJSON("/api/media", function(json) {
//	$.each(json, function(i, data){
//		jsonRet(data.name, data.uploadUrl);	
//	});	
//
//});
//
//
//var tinyMCEImageList = jsonRet();
//
//console.log(tinyMCEImageList);

var mediaList = new ApiDataList("/api/media/image");
var tinyMCEImageList = mediaList.getTinyMCEListDataByMapList(["name","uploadUrl"]);
