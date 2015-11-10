function upload(id) {/*上传附件*/
	var type= id.value.match(/^(.*)(\.)(.{1,8})$/)[3].toLowerCase();
	var size=$(id).get(0).files[0].size;
	var name=$(id).get(0).files[0].name;
	if(size>20*1024*1024){
		layer.alert("附件最大20M,请选择小于此大小的文件，或拆分上传");	
		return;
	}
	var size2=0;
	if(type=="pdf"||type=='xlsx'||type=="word"||type=="excel"||type=="txt"||type=="jpg"||type=="png"||type=="bmp"||type=="gif"){
	$.ajaxFileUpload( {
		url : '/index.php/FileUpload/index.html',
		secureuri : false,
		fileElementId : 'file',
		dataType : 'json',
		success : function(data, status) {
			if (typeof (data.error) != 'undefined') {
				if (data.error != '') {
					//alert(data.error);
					layer.alert(data.error);
				} else {
					//alert(data.url);
					var guid=data.url;
					size2=(size/1024).toFixed(2);
					var href=$(".fileaddnew").attr("guid");
					$(".fileaddnew").append("<li>"+name+"&nbsp;（大小："+size2+"KB） <a  href='/index.php/FileUpload/download.html?id="+guid+"' gu='"+guid+"' siz='"+size+"'  nam='"+name+"' class='btn_class btn_file_look'>查看</a>"
							+" &nbsp;&nbsp;<a href='javascript:void(0)' class='btn_class btn_file_delete'>删除</a><br/></li>");
				}
			}
		},
		error : function(data, status, e) {
			//alert(e);
			layer.alert(e);
		}
	})}
	else{
		layer.alert("请选择文件格式为PDF、Word、Excel、Txt、JPG、PNG、BMP、GIF的文件");
		//layeralert("请选择文件格式为PDF、Word、Excel、Txt、JPG、PNG、BMP、GIF的文件",succ,"文件格式不正确");		
	}
}

function uploadimg(id){/*上传img*/
	var type= id.value.match(/^(.*)(\.)(.{1,8})$/)[3].toLowerCase();
	if(type=="jpg"||type=="jpeg"||type=="bmp"||type=="png"){
	$.ajaxFileUpload( {
		url : '/index.php/notice/ImgUploadte',
		secureuri : false,
		fileElementId : 'file',
		dataType : 'json',
		success : function(data, status) {
			if (typeof (data.error) != 'undefined') {
				if (data.error != '') {
					//alert(data.error);
					layer.alert(data.error);
				} else {
					//alert(data.msg);
					$(".imgnewadd").css("display","block");
					$(".div_img_upload span").css("display","none");
					//$(".imgnewadd").attr("u");
					$(".imgnewadd").attr("src",$(".imgnewadd").attr("u")+data.url);
					$(".imgnewadd").attr("t",data.url);
				}
			}
		},
		error : function(data, status, e) {
			//alert(e);
			layer.alert(e);
		}
	})}
	else{
		alert("支持JPG、JPEG、BMP、PNG格式的图片");
		//layeralert("请选择文件格式为PDF、Word、Excel、Txt、JPG、PNG、BMP、GIF的文件",succ,"文件格式不正确");		
	}	
}