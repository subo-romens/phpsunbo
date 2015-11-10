
var MedicineDetail;

$(document).on("click", ".font", function () {
    var size = $(this).attr("size");
    if (size == "0") {
        $(this).attr("size","1");
        $(".MedicineTitle").css("font-size", "20px");
        $(".MedicineContent").css("font-size", "16px");
    }
    else {
        $(this).attr("size", "0");
        $(".MedicineTitle").css("font-size", "16px");
        $(".MedicineContent").css("font-size", "14px");
    }
});


$(function () {
    var MedicineGuid = getQueryStringByName("MedicineGuid") || sessionStorage.getItem("MedicineGuid") || getQueryStringByName("GUID");
    if (MedicineGuid) {
        if (getQueryStringByName("GUID")) {
            $(".divtop_div1").remove();
        }
        var urlData = "action=getMedicineById&MedicineGuid=" + MedicineGuid + "&" + Math.random();
        var successFun = function (data) {
            if (data != undefined || data != null) {
                MedicineDetail = data;
                var html = "";
                html += '<div class="MedicineTitle">' + MedicineDetail[0]["MedicineTitle"] + '</div>';
                html += '<div class="MedicineContent">';
                html += '<p class="InfoTitle">【药品名称】</p>';
                html += '<p>英文名:' + MedicineDetail[0]["EnglishName"] + '</p>';
                html += '<p>商品名:' + MedicineDetail[0]["MedicineTitle"] + '</p>';
                html += '<p class="InfoTitle">【批准文号】</p>';
                html += '<p>' + MedicineDetail[0]["PZWH"] + '</p>';
                html += '<p class="InfoTitle">【生产企业】</p>';
                html += '<p>' + MedicineDetail[0]["FactoryName"] + '</p>';
                html += '<p class="InfoTitle">【功能主治】</p>';
                html += '<p>' + MedicineDetail[0]["zz"] + '</p>';
                html += '<p class="InfoTitle">【药理毒理】</p>';
                html += '<p>' + MedicineDetail[0]["yldl"] + '</p>';
                html += '<p class="InfoTitle">【药物相互作用】</p>';
                html += '<p>' + MedicineDetail[0]["xhzy"] + '</p>';
                html += '<p class="InfoTitle">【不良反应】</p>';
                html += '<p>' + MedicineDetail[0]["blfy"] + '</p>';
                html += '<p class="InfoTitle">【禁忌】</p>';
                html += '<p>' + MedicineDetail[0]["jj"] + '</p>';
                html += '<p class="InfoTitle">【产品规格】</p>';
                html += '<p>' + MedicineDetail[0]["GG"] + '</p>';
                html += '<p class="InfoTitle">【用法用量】</p>';
                html += '<p>' + MedicineDetail[0]["yfyl"] + '</p>';
                html += '<p class="InfoTitle">【贮藏方法】</p>';
                html += '<p>' + MedicineDetail[0]["zcff"] + '</p>';
                html += '<p class="InfoTitle">【注意事项】</p>';
                html += '<p>' + MedicineDetail[0]["memo2"] + '</p>';
                html += '</div>';
                $(".divinfo").html(html);
            }
        }
        doAjaxGet("../Handle/MedicineListHandler.aspx", urlData, successFun, "");
    }
});