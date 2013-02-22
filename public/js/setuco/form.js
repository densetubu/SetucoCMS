
//todo: setTimeoutでてきとーな時間遅らせてイベントセットするんじゃなくてDOM構築完了時点で読み込ませるようにする
setTimeout(function() {

    //多重送信防止
    $.fn.disableDoubleSubmit = function() {
        //ボタン内のname,valueを送信
        $('input[type=submit],input[type=button],button').click(function(){
            var name = $(this).attr('name');
            var value = $(this).attr('value');
            $(this).parent().append('<input type="hidden" name="'+ name +'" value="'+ value +'">');
        });

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

}, 1000);

