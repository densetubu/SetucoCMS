dojo.require('dojo.NodeList-traverse');
dojo.require('dojo.NodeList-html');

/********************
 * global functions
 ********************/
function hideFlashMessage() {
    dojo.style('messageArea', {display: 'none'});  
}
function confirmDeleteRedirect(url, item){
    if (confirm(item + "を本当に削除してよろしいですか？")) {
        location.href = url;
    }
}

/********************
 * index/index
 ********************/
function showAmbitionEdit() {
    dojo.style('ambitionView', {display: 'none'});
    dojo.style('ambitionEdit', {display: 'inline'});
}
function hideAmbitionEdit() {
    dojo.style('ambitionEdit', {display: 'none'});
    dojo.style('ambitionView', {display: 'inline'});
}

/********************
 * directory/index
 ********************/
function openCategory(switcher, openSrcPath, closeSrcPath) {
    dojo.query(switcher.parentNode).next().style('display', 'block');
    dojo.query(switcher).children().at(0).attr('src', openSrcPath);
    dojo.query(switcher).attr('onclick', 'closeCategory(this,\'' + openSrcPath + '\' ,\'' + closeSrcPath + '\')');
}
function closeCategory(switcher, openSrcPath, closeSrcPath) {
    dojo.query(switcher.parentNode).next().style('display', 'none');
    dojo.query(switcher).children().at(0).attr('src', closeSrcPath);
    dojo.query(switcher).attr('onclick', 'openCategory(this,\'' + openSrcPath + '\' ,\'' + closeSrcPath + '\')');
}

/********************
 * page/index
 ********************/
function showPageElementEdit(switcher) {
    dojo.query(switcher).parent().next().style('display', 'inline');
    defaultHtml = switcher.innerHTML;
}
function hidePageElementEdit(switcher) {
    dojo.query(switcher).parent().prev().children().at(0).html(defaultHtml);
    dojo.query(switcher).parent().style('display', 'none');
}

/********************
 * tag/index
 ********************/
function showTagEdit(switcher) {
    dojo.query(switcher.parentNode).prev().children().at(0).style('display', 'none');
    dojo.query(switcher.parentNode).prev().children().at(1).style('display', 'block');
}
function hideTagEdit(switcher) {
    dojo.query(switcher.parentNode.parentNode).style('display', 'none');
    dojo.query(switcher.parentNode.parentNode).prev().style('display', 'inline');
}

