<script>
	$( document ).ready(function() {
		fn_modalShowMsg(<?php echo $id;?>)
	});
</script>
<div id="content_list">
<div id="article_content">
	<div style="margin:30px 0;"><b><a href="/fbactivity">&lt;&lt;回首頁</a></b></div>
	<div class="fb-thumb"><img src="<?php echo $fb_userpic;?>"></div>
	<div class="fb-info">
		<div class="fb-username"><a href="https://facebook.com/<?php echo $fb_userid;?>" target="_blank"><?php echo $fb_username;?></a></div>
		<div class="fb-time"><?php echo $ctime;?></div>
	</div>
	<div class="clearfix"></div>
<?php
	if(file_exists(BASEPATH . "../uploads/fbactivity/" . $id . ".jpg")) {
?>
<div class="item-photo"><img src="<?php echo base_url("fbactivity/photo/" . $id);?>"></div>
<?php
	}
?>
	
	<div class="item-action">
		<span><a href="#" onclick="return fn_CallFbLike('<?php echo $id;?>');">讚</a></span>·
		<span><a href="javascript:fn_CallFbShare('<?php echo $id;?>');">分享</a></span>·
		<span>
			<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
			<span class="UFIBlingBoxText" id="like-text-<?php echo $id;?>"><?php echo $like_num;?></span>
		</span>
		<span>
			<i class="fa fa-share-alt" aria-hidden="true"></i>
			<span class="UFIBlingBoxText" id="share-text-<?php echo $id;?>"><?php echo $share_num;?></span>
		</span>
	</div>

	<div id="comment_box">
	</div>
</div>
</div>

