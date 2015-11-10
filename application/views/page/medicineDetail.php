<!DOCTYPE html>
<html class="ui-mobile">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!--<base href="http://health.yiyao365.cn/medicine/medicineDetail.html">-->
<base href=".">

<meta name="viewport"
	content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0,maximum-scale=1, user-scalable=no">
<title></title>
<link rel="stylesheet" type="text/css"
	href="/ui/css/jquery.mobile-1.3.2.css">
<script src="/ui/js/jquery.js"></script>
<script src="/ui/js/jquery.mobile-1.3.2.js"></script>
<script src="/ui/js/jsmainin.js"></script>
<link rel="stylesheet" type="text/css"
	href="/ui/css/mainstyle.css">
<script src="/ui/js/medicineDetail.js"></script>
<link rel="stylesheet" type="text/css"
	href="/ui/css/medicineDetail.css">
</head>
<body class="ui-mobile-viewport ui-overlay-c">
	<div data-role="page" data-url="/medicine/medicineDetail.html"
		tabindex="0"
		class="ui-page ui-body-c ui-page-header-fixed ui-page-active"
		style="padding-top: 37px; min-height: 410px; margin-top: -40px;">
		<div class="divclear0"></div>
		<div class="divtop">
			<div style="margin: 5px 0;" class="divinfo">
				<div class="MedicineTitle"><?php echo $da[0]['MedicineTitle']?></div>
				<div class="MedicineContent">
					<p class="InfoTitle">【药品名称】</p>
					<p>英文名:<?php echo $da[0]['EnglishName']?></p>
					<p>商品名:<?php echo $da[0]['MedicineTitle']?></p>
					<p class="InfoTitle">【批准文号】</p>
					<p><?php echo $da[0]['PZWH']?></p>
					<p class="InfoTitle">【生产企业】</p>
					<p><?php echo $da[0]['FactoryName']?></p>
					<p class="InfoTitle">【功能主治】</p>
					<p><?php echo $da[0]['zz']?></p>
					<p class="InfoTitle">【药理毒理】</p>
					<p><?php echo $da[0]['yldl']?></p>
					<p class="InfoTitle">【药物相互作用】</p>
					<p><?php echo $da[0]['xhzy']?></p>
					<p class="InfoTitle">【不良反应】</p>
					<p><?php echo $da[0]['blfy']?></p>
					<p class="InfoTitle">【禁忌】</p>
					<p><?php echo $da[0]['jj']?></p>
					<p class="InfoTitle">【产品规格】</p>
					<p><?php echo $da[0]['GG']?></p>
					<p class="InfoTitle">【用法用量】</p>
					<p><?php echo $da[0]['yfyl']?></p>
					<p class="InfoTitle">【贮藏方法】</p>
					<p><?php echo $da[0]['zcff']?></p>
					<p class="InfoTitle">【注意事项】</p>
					<p><?php echo $da[0]['memo2']?></p>
				</div>
			</div>
		</div>


	</div>
</body>
</html>