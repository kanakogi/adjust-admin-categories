jQuery(function($) {
    //1つ目以外を見えなくする
    $('.aac_tab li:first-child').addClass('select');
    $('.aac_content:not(:first-child)').addClass('hide');

    //クリックしたときのファンクションをまとめて指定
    $('.aac_tab li').click(function() {

        //.index()を使いクリックされたタブが何番目かを調べ、
        //indexという変数に代入します。
        var index = $('.aac_tab li').index(this);

        //コンテンツを一度すべて非表示にし、
        $('.aac_contents .aac_content').css('display','none');

        //クリックされたタブと同じ順番のコンテンツを表示します。
        $('.aac_contents .aac_content').eq(index).css('display','block');

        //一度タブについているクラスselectを消し、
        $('.aac_tab li').removeClass('select');

        //クリックされたタブのみにクラスselectをつけます。
        $(this).addClass('select')
    });
});