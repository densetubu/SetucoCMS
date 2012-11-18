dojo.require('dojo.NodeList-traverse');
dojo.require('dojo.NodeList-html');

/********************
 * global functions
 ********************/
function hideMessageArea() {
    $('.messageArea').css('display', 'none');
}
function hideErrorMessageArea() {
    $('.errorMessageArea').css('display', 'none');
}
function confirmDeleteRedirect(url, item){
    if (confirm("「" + item + "」を本当に削除してよろしいですか？")) {
        location.href = url;
    }
}

/********************
 * index/index
 ********************/
function showAmbitionEdit() {
    $('#ambitionView').css('display', 'none');
    $('#ambitionEdit').css('display', 'inline');
}
function hideAmbitionEdit() {
    $('#ambitionView').css('display', 'inline');
    $('#ambitionEdit').css('display', 'none');
}

/********************
 * directory/index
 ********************/
function openCategory(switcher, openSrcPath, closeSrcPath) {
    $(switcher.parentNode).next().css('display', 'block');
    $(switcher).children('img').attr('src', openSrcPath);
    $(switcher).attr('onclick', 'closeCategory(this,\'' + openSrcPath + '\' ,\'' + closeSrcPath + '\')');
}
function closeCategory(switcher, openSrcPath, closeSrcPath) {
    $(switcher.parentNode).next().css('display', 'none');
    $(switcher).children('img').attr('src', closeSrcPath);
    $(switcher).attr('onclick', 'openCategory(this,\'' + openSrcPath + '\' ,\'' + closeSrcPath + '\')');
}

/********************
 * page/index
 ********************/
function showPageElementEdit(switcher) {
    $(switcher).parent().next().css('display', 'inline');
    defaultHTML = switcher.innerHTML;
}
function hidePageElementEdit(switcher) {
    $(switcher).parent().prev().children('select').html(defaultHTML);
    $(switcher).parent().css('display', 'none');
}

/*********************
 * category/index
 * tag/index
 *********************/
function showRowEdit(switcher) {
    $(switcher.parentNode).prev().children('span').css('display', 'none');
    $(switcher.parentNode).prev().children('div').css('display', 'block');
}
function hideRowEdit(switcher) {
    $(switcher.parentNode.parentNode.parentNode).css('display', 'none');
    $(switcher.parentNode.parentNode.parentNode).prev().css('display', 'block');
}

/********************
 * media/index
 ********************/
var uploadImgIndex = 1;

function addUploadImgForm(switcher) {
    uploadImgIndex++;

    $("#upload_img" + uploadImgIndex).css('display', 'block');
    if (uploadImgIndex == 5) {
        $('#upload_img_add').parent().css('display', 'none');
    }
}

