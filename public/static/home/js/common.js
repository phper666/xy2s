/*学校选择*/
$(document).ready(function(){
    $(".check_area").change(function(){
        if($(this).val() == 1){
            $(".check_school").css('display','none');
            $(".check_school1").css('display','none');
            $(".check_school2").css('display','block');
        }else if($(this).val() == 2){
            $(".check_school").css('display','block');
            $(".check_school1").css('display','none');
            $(".check_school2").css('display','none');
        }else if($(this).val() == 3){
            $(".check_school").css('display','none');
            $(".check_school2").css('display','none');
            $(".check_school1").css('display','block');
        }

        //鼠标点击学校事件
        $(".theme-signin input[type=text]").click(function(){
            var a = $(this).attr('class');    //获取点击input的类名
            //循环的给每一个不是点击的类赋值disabled为true
            $(".theme-signin input[type=text]").each(function(){
                if($(this).attr('class') != a){
                    //如果不等于点击的类，就赋值disabled为true
                    $(this).attr('disabled',true);
                    if($(this).attr('class')=='a4'){
                        $(this).attr('disabled',true);
                    }
                    $('.theme-signin').submit();
                    //alert($(this).attr('class'));
                }
            });
        });
    });
});
/*一个简单的jq特效，导航选中状态*/
$(document).ready(function () {
    $(function () {
        var urlstr = location.href;
        //alert((urlstr + ‘/’).indexOf($(this).attr(‘href’)));
        var urlstatus = false;
        $(".nav_li a").each(function () {
            if ((urlstr + '/').indexOf($(this).attr('href')) > -1 && $(this).attr('href') != '') {
                $(this).addClass('cur');
                urlstatus = true;
            } else {
                $(this).removeClass('cur');
            }
        });
        if (!urlstatus) {
            $("#menu a").eq(0).addClass('cur');
        }
    });
});





