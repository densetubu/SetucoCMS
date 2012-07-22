
$(function() {
    //this event from http://www.alink.co.jp/tech/blog/2009/04/05/javascript-jquery%E3%81%A7%E3%83%95%E3%82%A9%E3%83%BC%E3%83%A0%E3%81%AE2%E9%87%8D%E9%80%81%E4%BF%A1%E3%82%92%E9%98%B2%E3%81%90/
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

});

