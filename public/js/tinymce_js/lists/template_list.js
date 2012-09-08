// This list may be created by a server logic page PHP/ASP/ASPX/JSP in some backend system.
// There templates will be displayed as a dropdown in all media dialog if the "template_external_list_url"
// option is defined in TinyMCE init.

var templateList = new ApiDataList("/api/template");
var tinyMCETemplateList = templateList.getTinyMCEListDataByMapList(["title", "url", "explanation"]);
