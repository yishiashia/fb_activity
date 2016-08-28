<html>
<title>Facebook投票活動測試頁</title>
<meta charset="UTF-8">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/font-awesome.min.css">
<link rel="stylesheet" href="/css/fbactivity.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<style>
.fb-comments, .fb-comments iframe {width: 500px !important;}
.item {
	float: left;
	border: 1px solid #b1aeae;
	padding: 20px;
	margin: 20px;
	width: 320px;
}
.item#modalMsgBox {
	border: 0px;
	width: 100%;
}
.item img {
	width: 280px;
}
.fb-thumb img {
	width: 60px;
}
.photo-box img {
	width: 60px;
}
</style>
</head>
<script>

var FBUser = {};
FBUser.islogin = false;

function statusChangeCallback(response) {
	FBUser.id = response.authResponse.userID;
	FBUser.token = response.authResponse.accessToken;

	FB.api('/'+response.authResponse.userID+'/picture?redirect=false&width=200&height=200&type=normal', function(response){
		if (response && !response.error) {
			$('#userphoto').attr('src', response.data.url);
			$('#fbPicture').val(response.data.url);
			FBUser.pic = response.data.url;
		}
	});

	if (response.status === 'connected') {
		getFBUser();
	} else if (response.status === 'not_authorized') {
		document.getElementById('status').innerHTML = 'Please log ' +
			'into this app.';
	} else {
		document.getElementById('status').innerHTML = 'Please log ' +
			'into Facebook.';
	}
}

function checkLoginState() {
	FB.getLoginStatus(function(response) {
		statusChangeCallback(response);
	});
}

window.fbAsyncInit = function() {
	FB.init({
		appId: '<?php echo $fb_appid; ?>',
		cookie: true,
		xfbml: true,
		version: 'v2.3'
	});

	FB.getLoginStatus(function(response) {
		statusChangeCallback(response);
	});
};

// Load the SDK asynchronously
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "https://connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function getFBUser() {
	FB.api('/me', function(response) {
		$('#fbUsername').val(response.name);
		$('#fbUserID').val(response.id);
		$('#fbEmail').val(response.email);
		FBUser.name = response.name;
		FBUser.islogin = true;
	});
}

function fn_modalShowMsg(id) {
	$("#comment_box").html('<div id="fb_modalComment" style="width: 568px;" class="fb-comments" data-href="<?php echo base_url("fbactivity/article/p");?>' + id + '.html" data-version="v2.3"></div>');
	FB.XFBML.parse($('#comment_box').get(0));

	var request_url = "<?php echo base_url("fbactivity/pdata");?>/" + id;
	$.get(request_url , function(resp){
		resp = JSON.parse(resp);
		$('#modalMsgBox .item-word').html(resp[0].message);
		$('#modalMsgBox .fb-thumb img').attr("src", resp[0].fb_userpic);
		$('#modalMsgBox .fb-username a').attr("href", "https://facebook.com/" + resp[0].fb_userid);
		$('#modalMsgBox .fb-username a').html(resp[0].fb_username);
		$('#modalMsgBox .fb-time').html(resp[0].lastmod);
		if(resp[0].img == 1) {
			$('#modalMsgBox .item-photo').html('<img src="<?php echo base_url("uploads")?>/' + id + '.jpg">');
		} else {
			$('#modalMsgBox .item-photo').html('');
		}
		$("#modalMsgBox #like-text-modal").html(resp[0].like_num);
		$("#modalMsgBox #share-text-modal").html(resp[0].share_num);

		$("#like_link").attr("href", "javascript:fn_CallFbLike('" + id + "');");
		$("#share_link").attr("href", "javascript:fn_CallFbShare('" + id + "');");
	});
	return false;
}

function fn_CallFbShare(dID){
	FB.ui({
		method: 'feed',
		link: '<?php echo base_url("fbactivity/article/p");?>'+dID+'.html',
	}, function(response){
console.log(response);
		if(response.post_id){
			$.post("<?php echo base_url("fbactivity/share");?>",{"action":"SaveFeedData","pid":dID,"fbPostID":response.post_id},function(data){
console.log(data);
				if(data>=0) {
					$("#share-text-"+dID).html(data);
					$("#share-text-modal").html(data);
					alert('分享完成');
				}
			});
		}
	});
}

function fn_CallFbLike(pid) {
	if(FBUser.islogin) {
		$.post("<?php echo base_url("fbactivity/like");?>",{"action":"SaveVoteData","pid":pid},function(data) {
			if(data<0){
				alert("您已經按過讚囉!");
			} else {
				FB.ui({
					method: 'feed',
					link: '<?php echo base_url("fbactivity/article/p");?>'+pid+'.html',
				}, function(response){});
				$("#like-text-"+pid).html(data);
				$("#like-text-modal").html(data);
			}
			return false;
		});
	} else {
		alert("請先登入Facebook");
		return false;
	}
	return false;
}

function check_login() {
	if(FBUser.islogin) {
		if($('#frm-Comment').val()=="") {
			alert("請輸入你要說的話");
			return false;
		} else {
			return true;
		}
	} else {
		alert("請先登入Facebook");
		return false;
	}
}
</script>
