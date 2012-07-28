
//todo: setTimeoutでてきとーな時間遅らせてイベントセットするんじゃなくてDOM構築完了時点で読み込ませるようにする
setTimeout(function() {

    //多重送信防止
    $.fn.disableDoubleSubmit = function() {
        $(this).bind("submit",function() {
            //ページ内に存在する全てのボタンを無効化させる
            $('button').each(function (i) {
                $(this).attr("disabled", true);
            });
            $('input').each(function (i) {
                if ($(this).attr('type') === 'submit' || $(this).attr('type') === 'button') {
                    $(this).attr("disabled", true);
                }
            });

        });
        return this;
    };


    $("form").disableDoubleSubmit();

}, 500);

