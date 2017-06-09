$(function(){
	var bankChoice = {
		'1':'中国银行',
		'2':'工商银行',
		'3':'农业银行',
		'4':'交通银行',
		'5':'广发银行',
		'6':'深发银行',
		'7':'建设银行',
		'8':'上海浦发银行',
		'9':'浙江泰隆商业银行',
		'10':'招商银行',
		'11':'邮政储蓄银行',
		'12':'民生银行',
		'13':'兴业银行',
		'14':'广东发展银行',
		'15':'东莞银行',
		'16':'深圳发展银行',
		'17':'中信银行',
		'18':'华夏银行',
		'19':'中国光大银行',
		'20':'北京银行',
		'21':'上海银行',
		'22':'天津银行',
		'23':'大连银行',
		'24':'杭州银行',
		'25':'宁波银行',
		'26':'厦门银行',
		'27':'广州银行',
		'28':'平安银行',
		'29':'浙商银行',
		'30':'上海农村商业银行',
		'31':'重庆银行',
		'32':'江苏银行',
		'33':'农村信用合作社',
		'34':'广州农村商业银行',
		'35':'四川成都龙泉驿稠州村镇银行',
		'36':'内蒙古银行',
		'37':'深圳农村商业银行',
		'38':'贵阳银行',
		'39':'贵州银行',
		'40':'贵阳农村商业银行',
		'41':'南充市商业银行',
		'42':'东莞农商银行',
		'43':'徽商银行',
		'44':'河北银行',
		'45':'重庆农村商业银行',
		'46':'呼和浩特金谷农村商业银行',
		'47':'吴江农村商业银行',
		'48':'宁夏银行',
		'49':'石嘴山银行',
		'50':'黄河农村商业银行',
		'51':'德阳银行',
		'52':'昆明富滇银行',
		'53':'云南省农村信用社',
		'54':'郑州银行',
		'55':'潍坊银行',
		'56':'渤海银行',
		'57':'安徽凤阳农村商业银行',
		'58':'富滇银行',
		'59':'玉溪市商业银行',
		'60':'曲靖市商业银行',
		'61':'泰安市商业银行',
		'62':'汇丰银行',
		'63':'河北邯郸农村信用社',
		'64':'江苏江南农村商业银行',
		'65':'湖北省农村信用社',
		'66':'湖北银行',
		'67':'汉口银行',
		'68':'襄阳市农村商业银行',
		'69':'南京银行',
		'70':'贵州花溪农村商业银行',
		'71':'包商银行',
		'72':'柳州银行',
		'73':'广西农村信用社 ',
		'74':'桂林银行',
		'75':'广西北部湾银行',
		'76':'贵州贞丰农村商业银行股份有限公司',
		'77':'四川农村信用社',
		'78':'长春农商银行',
		'79':'吉林省农业信用社',
		'80':'吉林银行',
		'81':'浙江农信银行',
		'82':'苏州银行',
		'83':'江苏长江商业银行',
		'84':'北京农村商业银行',
		'85':'合肥科技农村商业银行',
		'86':'湖北嘉鱼农村商业银行',
		'87':'广东顺德农村商业银行',
		'88':'恒丰银行',
		'89':'九江银行'
	};

	var province = {
		'1':'北京市',
		'2':'天津市',
		'3':'河北省',
		'4':'山西省',
		'5':'内蒙古自治区',
		'6':'辽宁省',
		'7':'吉林省',
		'8':'黑龙江省',
		'9':'上海市',
		'10':'江苏省',
		'11':'浙江省',
		'12':'安徽省',
		'13':'福建省',
		'14':'江西省',
		'15':'山东省',
		'16':'河南省',
		'17':'湖北省',
		'18':'湖南省',
		'19':'广东省',
		'20':'广西壮族自治区',
		'21':'海南省',
		'22':'重庆市',
		'23':'四川省',
		'24':'贵州省',
		'25':'云南省',
		'26':'西藏自治区',
		'27':'陕西省',
		'28':'甘肃省',
		'29':'青海省',
		'30':'宁夏回族自治区',
		'31':'新疆维吾尔自治区',
		'32':'香港特别行政区',
		'33':'澳门特别行政区',
		'34':'台湾省'
	};
	var city = {
		'1001':['1','北京市'],
		'1002':['2','天津市'],
		'1003':['3','石家庄市'],
		'1004':['3','唐山市'],
		'1005':['3','秦皇岛市'],
		'1006':['3','邯郸市'],
		'1007':['3','邢台市'],
		'1008':['3','保定市'],
		'1009':['3','张家口市'],
		'1010':['3','承德市'],
		'1011':['3','沧州市'],
		'1012':['3','廊坊市'],
		'1013':['3','衡水市'],
		'1014':['4','太原市'],
		'1015':['4','大同市'],
		'1016':['4','阳泉市'],
		'1017':['4','长治市'],
		'1018':['4','晋城市'],
		'1019':['4','朔州市'],
		'1020':['4','晋中市'],
		'1021':['4','运城市'],
		'1022':['4','忻州市'],
		'1023':['4','临汾市'],
		'1024':['4','吕梁市'],
		'1025':['5','呼和浩特市'],
		'1026':['5','包头市'],
		'1027':['5','乌海市'],
		'1028':['5','赤峰市'],
		'1029':['5','通辽市'],
		'1030':['5','鄂尔多斯市'],
		'1031':['5','呼伦贝尔市'],
		'1032':['5','巴彦淖尔市'],
		'1033':['5','乌兰察布市'],
		'1034':['5','兴安盟'],
		'1035':['5','锡林郭勒盟'],
		'1036':['5','阿拉善盟'],
		'1037':['6','沈阳市'],
		'1038':['6','大连市'],
		'1039':['6','鞍山市'],
		'1040':['6','抚顺市'],
		'1041':['6','本溪市'],
		'1042':['6','丹东市'],
		'1043':['6','锦州市'],
		'1044':['6','营口市'],
		'1045':['6','阜新市'],
		'1046':['6','辽阳市'],
		'1047':['6','盘锦市'],
		'1048':['6','铁岭市'],
		'1049':['6','朝阳市'],
		'1050':['6','葫芦岛市'],
		'1051':['7','长春市'],
		'1052':['7','吉林市'],
		'1053':['7','四平市'],
		'1054':['7','辽源市'],
		'1055':['7','通化市'],
		'1056':['7','白山市'],
		'1057':['7','松原市'],
		'1058':['7','白城市'],
		'1059':['7','延边朝鲜族自治州'],
		'1060':['8','哈尔滨市'],
		'1061':['8','齐齐哈尔市'],
		'1062':['8','鸡西市'],
		'1063':['8','鹤岗市'],
		'1064':['8','双鸭山市'],
		'1065':['8','大庆市'],
		'1066':['8','伊春市'],
		'1067':['8','佳木斯市'],
		'1068':['8','七台河市'],
		'1069':['8','牡丹江市'],
		'1070':['8','黑河市'],
		'1071':['8','绥化市'],
		'1072':['8','大兴安岭地区'],
		'1073':['9','上海市'],
		'1074':['10','南京市'],
		'1075':['10','无锡市'],
		'1076':['10','徐州市'],
		'1077':['10','常州市'],
		'1078':['10','苏州市'],
		'1079':['10','南通市'],
		'1080':['10','连云港市'],
		'1081':['10','淮安市'],
		'1082':['10','盐城市'],
		'1083':['10','扬州市'],
		'1084':['10','镇江市'],
		'1085':['10','泰州市'],
		'1086':['10','宿迁市'],
		'1087':['11','杭州市'],
		'1088':['11','宁波市'],
		'1089':['11','温州市'],
		'1090':['11','嘉兴市'],
		'1091':['11','湖州市'],
		'1092':['11','绍兴市'],
		'1093':['11','金华市'],
		'1094':['11','衢州市'],
		'1095':['11','舟山市'],
		'1096':['11','台州市'],
		'1097':['11','丽水市'],
		'1098':['12','合肥市'],
		'1099':['12','芜湖市'],
		'1100':['12','蚌埠市'],
		'1101':['12','淮南市'],
		'1102':['12','马鞍山市'],
		'1103':['12','淮北市'],
		'1104':['12','铜陵市'],
		'1105':['12','安庆市'],
		'1106':['12','黄山市'],
		'1107':['12','滁州市'],
		'1108':['12','阜阳市'],
		'1109':['12','宿州市'],
		'1110':['12','巢湖市'],
		'1111':['12','六安市'],
		'1112':['12','亳州市'],
		'1113':['12','池州市'],
		'1114':['12','宣城市'],
		'1115':['13','福州市'],
		'1116':['13','厦门市'],
		'1117':['13','莆田市'],
		'1118':['13','三明市'],
		'1119':['13','泉州市'],
		'1120':['13','漳州市'],
		'1121':['13','南平市'],
		'1122':['13','龙岩市'],
		'1123':['13','宁德市'],
		'1124':['14','南昌市'],
		'1125':['14','景德镇市'],
		'1126':['14','萍乡市'],
		'1127':['14','九江市'],
		'1128':['14','新余市'],
		'1129':['14','鹰潭市'],
		'1130':['14','赣州市'],
		'1131':['14','吉安市'],
		'1132':['14','宜春市'],
		'1133':['14','抚州市'],
		'1134':['14','上饶市'],
		'1135':['15','济南市'],
		'1136':['15','青岛市'],
		'1137':['15','淄博市'],
		'1138':['15','枣庄市'],
		'1139':['15','东营市'],
		'1140':['15','烟台市'],
		'1141':['15','潍坊市'],
		'1142':['15','济宁市'],
		'1143':['15','泰安市'],
		'1144':['15','威海市'],
		'1145':['15','日照市'],
		'1146':['15','莱芜市'],
		'1147':['15','临沂市'],
		'1148':['15','德州市'],
		'1149':['15','聊城市'],
		'1150':['15','滨州市'],
		'1151':['15','菏泽市'],
		'1152':['16','郑州市'],
		'1153':['16','开封市'],
		'1154':['16','洛阳市'],
		'1155':['16','平顶山市'],
		'1156':['16','安阳市'],
		'1157':['16','鹤壁市'],
		'1158':['16','新乡市'],
		'1159':['16','焦作市'],
		'1160':['16','濮阳市'],
		'1161':['16','许昌市'],
		'1162':['16','漯河市'],
		'1163':['16','三门峡市'],
		'1164':['16','南阳市'],
		'1165':['16','商丘市'],
		'1166':['16','信阳市'],
		'1167':['16','周口市'],
		'1168':['16','驻马店市'],
		'1169':['17','武汉市'],
		'1170':['17','黄石市'],
		'1171':['17','十堰市'],
		'1172':['17','宜昌市'],
		'1173':['17','襄樊市'],
		'1174':['17','鄂州市'],
		'1175':['17','荆门市'],
		'1176':['17','孝感市'],
		'1177':['17','荆州市'],
		'1178':['17','黄冈市'],
		'1179':['17','咸宁市'],
		'1180':['17','随州市'],
		'1181':['17','恩施土家族苗族自治州'],
		'1182':['17','神农架'],
		'1183':['18','长沙市'],
		'1184':['18','株洲市'],
		'1185':['18','湘潭市'],
		'1186':['18','衡阳市'],
		'1187':['18','邵阳市'],
		'1188':['18','岳阳市'],
		'1189':['18','常德市'],
		'1190':['18','张家界市'],
		'1191':['18','益阳市'],
		'1192':['18','郴州市'],
		'1193':['18','永州市'],
		'1194':['18','怀化市'],
		'1195':['18','娄底市'],
		'1196':['18','湘西土家族苗族自治州'],
		'1197':['19','广州市'],
		'1198':['19','韶关市'],
		'1199':['19','深圳市'],
		'1200':['19','珠海市'],
		'1201':['19','汕头市'],
		'1202':['19','佛山市'],
		'1203':['19','江门市'],
		'1204':['19','湛江市'],
		'1205':['19','茂名市'],
		'1206':['19','肇庆市'],
		'1207':['19','惠州市'],
		'1208':['19','梅州市'],
		'1209':['19','汕尾市'],
		'1210':['19','河源市'],
		'1211':['19','阳江市'],
		'1212':['19','清远市'],
		'1213':['19','东莞市'],
		'1214':['19','中山市'],
		'1215':['19','潮州市'],
		'1216':['19','揭阳市'],
		'1217':['19','云浮市'],
		'1218':['20','南宁市'],
		'1219':['20','柳州市'],
		'1220':['20','桂林市'],
		'1221':['20','梧州市'],
		'1222':['20','北海市'],
		'1223':['20','防城港市'],
		'1224':['20','钦州市'],
		'1225':['20','贵港市'],
		'1226':['20','玉林市'],
		'1227':['20','百色市'],
		'1228':['20','贺州市'],
		'1229':['20','河池市'],
		'1230':['20','来宾市'],
		'1231':['20','崇左市'],
		'1232':['21','海口市'],
		'1233':['21','三亚市'],
		'1234':['22','重庆市'],
		'1235':['23','成都市'],
		'1236':['23','自贡市'],
		'1237':['23','攀枝花市'],
		'1238':['23','泸州市'],
		'1239':['23','德阳市'],
		'1240':['23','绵阳市'],
		'1241':['23','广元市'],
		'1242':['23','遂宁市'],
		'1243':['23','内江市'],
		'1244':['23','乐山市'],
		'1245':['23','南充市'],
		'1246':['23','眉山市'],
		'1247':['23','宜宾市'],
		'1248':['23','广安市'],
		'1249':['23','达州市'],
		'1250':['23','雅安市'],
		'1251':['23','巴中市'],
		'1252':['23','资阳市'],
		'1253':['23','阿坝藏族羌族自治州'],
		'1254':['23','甘孜藏族自治州'],
		'1255':['23','凉山彝族自治州'],
		'1256':['24','贵阳市'],
		'1257':['24','六盘水市'],
		'1258':['24','遵义市'],
		'1259':['24','安顺市'],
		'1260':['24','铜仁地区'],
		'1261':['24','黔西南布依族苗族自治州'],
		'1262':['24','毕节地区'],
		'1263':['24','黔东南苗族侗族自治州'],
		'1264':['24','黔南布依族苗族自治州'],
		'1265':['25','昆明市'],
		'1266':['25','曲靖市'],
		'1267':['25','玉溪市'],
		'1268':['25','保山市'],
		'1269':['25','昭通市'],
		'1270':['25','丽江市'],
		'1271':['25','思茅市'],
		'1272':['25','临沧市'],
		'1273':['25','楚雄彝族自治州'],
		'1274':['25','红河哈尼族彝族自治州'],
		'1275':['25','文山壮族苗族自治州'],
		'1276':['25','西双版纳傣族自治州'],
		'1277':['25','大理白族自治州'],
		'1278':['25','德宏傣族景颇族自治州'],
		'1279':['25','怒江傈僳族自治州'],
		'1280':['25','迪庆藏族自治州'],
		'1281':['26','拉萨市'],
		'1282':['26','昌都地区'],
		'1283':['26','山南地区'],
		'1284':['26','日喀则地区'],
		'1285':['26','那曲地区'],
		'1286':['26','阿里地区'],
		'1287':['26','林芝地区'],
		'1288':['27','西安市'],
		'1289':['27','铜川市'],
		'1290':['27','宝鸡市'],
		'1291':['27','咸阳市'],
		'1292':['27','渭南市'],
		'1293':['27','延安市'],
		'1294':['27','汉中市'],
		'1295':['27','榆林市'],
		'1296':['27','安康市'],
		'1297':['27','商洛市'],
		'1298':['28','兰州市'],
		'1299':['28','嘉峪关市'],
		'1300':['28','金昌市'],
		'1301':['28','白银市'],
		'1302':['28','天水市'],
		'1303':['28','武威市'],
		'1304':['28','张掖市'],
		'1305':['28','平凉市'],
		'1306':['28','酒泉市'],
		'1307':['28','庆阳市'],
		'1308':['28','定西市'],
		'1309':['28','陇南市'],
		'1310':['28','临夏回族自治州'],
		'1311':['28','甘南藏族自治州'],
		'1312':['29','西宁市'],
		'1313':['29','海东地区'],
		'1314':['29','海北藏族自治州'],
		'1315':['29','黄南藏族自治州'],
		'1316':['29','海南藏族自治州'],
		'1317':['29','果洛藏族自治州'],
		'1318':['29','玉树藏族自治州'],
		'1319':['29','海西蒙古族藏族自治州'],
		'1320':['30','银川市'],
		'1321':['30','石嘴山市'],
		'1322':['30','吴忠市'],
		'1323':['30','固原市'],
		'1324':['30','中卫市'],
		'1325':['31','乌鲁木齐市'],
		'1326':['31','克拉玛依市'],
		'1327':['31','吐鲁番地区'],
		'1328':['31','哈密地区'],
		'1329':['31','昌吉回族自治州'],
		'1330':['31','博尔塔拉蒙古自治州'],
		'1331':['31','巴音郭楞蒙古自治州'],
		'1332':['31','阿克苏地区'],
		'1333':['31','克孜勒苏柯尔克孜自治州'],
		'1334':['31','喀什地区'],
		'1335':['31','和田地区'],
		'1336':['31','伊犁哈萨克自治州'],
		'1337':['31','塔城地区'],
		'1338':['31','阿勒泰地区'],
		'1339':['31','石河子市'],
		'1340':['31','阿拉尔市'],
		'1341':['31','图木舒克市'],
		'1342':['31','五家渠市'],
		'1343':['32','香港特别行政区'],
		'1344':['33','澳门特别行政区'],
		'1345':['34','台湾省'],
		'1346':['17','仙桃市'],
		'1347':['17','潜江市'],
		'1348':['17','天门市'],
		'1349':['21','东方市'],
		'1350':['16','济源市'],
		'1351':['21','五指山市'],
		'1352':['21','文昌市'],
		'1353':['21','万宁市'],
		'1354':['21','琼海市'],
		'1355':['21','澄迈县'],
		'1356':['21','定安县'],
		'1357':['21','琼中县'],
		'1358':['21','屯昌县'],
		'1359':['21','乐东县'],
		'1360':['21','昌江县'],
		'1361':['21','保亭县'],
		'1362':['21','陵水县'],
		'1363':['21','临高县'],
		'1364':['21','儋州市']
	};
	function tplOption(data){
		if(data){
			var optionStr = '<option value=""></option>';
			$.each(data,function(i,n){
				optionStr += '<option value="' + i + '">' + n + '</option>';
			})
		}
		return optionStr;
	}
	function tplCity(provId){
		var optionStr = '<option value=""></option>';
		$.each(city,function(i,n){
			if(n[0] == provId){
				optionStr += '<option value="' + i + '">' + n[1] + '</option>';
			}
		})
		$('.select2_city').html(optionStr);
		$('.select2_city').select2({
	        placeholder: "请选择城市",
	        allowClear: false
	    });
	}


	$('.select2_bank').html(tplOption(bankChoice));
	$('.select2_province').html(tplOption(province));

	$('.select2_bank').select2({
        placeholder: "请选择开户银行",
        allowClear: true
    });
    $('.select2_province').select2({
        placeholder: "请选择省份",
        allowClear: false
    });
    $('.select2_city').select2({
	        placeholder: "请选择城市",
	        allowClear: false
	    });

    $('.select2_province').change(function() {
    	var provId = $(this).val();
    	$('.select2_city').empty();
    	tplCity(provId);
	})
})



