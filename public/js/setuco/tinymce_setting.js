tinyMCE.init({
	// General options
	mode : "exact",
	elements : "page_contents",
	theme : "advanced",
	plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
	language : "ja",
	width:"652px",
	height:"300px",
	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontsizeselect,forecolor,backcolor,removeformat,|,link,unlink,media,image,|preview",
	theme_advanced_buttons2 : "bullist,numlist,|,search,replace,|,outdent,indent,blockquote,|tablecontrols,|,hr,visualaid,|,charmap,emotions,iespell,|,fullscreen,|,undo,redo,|,styleprops,code",
	theme_advanced_buttons3 : "",
	theme_advanced_buttons4 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	// Example content CSS (should be your site CSS)
	content_css : "css/content.css",
	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "lists/template_list.js",
	external_link_list_url : "lists/link_list.js",
	external_image_list_url : "/js/tinymce_js/lists/image_list.js",
	media_external_list_url : "lists/media_list.js",
	// Style formats
	style_formats : [
	{title : 'Bold text', inline : 'b'},
	{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
	{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
	{title : 'Example 1', inline : 'span', classes : 'example1'},
	{title : 'Example 2', inline : 'span', classes : 'example2'},
	{title : 'Table styles'},
	{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
	],
	
	// Replace values for the template plugin
	template_replace_values : {
	username : "Some User",
	staffid : "991234"
	}
});