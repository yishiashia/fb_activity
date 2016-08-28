<div id="content_list">
<?php
$row_count = count($rows);

for($i=0; $i<$row_count; ++$i) {
	$ctime = time() - strtotime($rows[$i]["lastmod"]);
	if($ctime<60) {
		$ctime = "Just now";
	} else if($ctime<3600) {
		$ctime = floor($ctime/60);
		if($ctime==1) {
			 $ctime = "1 min ago";
		} else {
			 $ctime = $ctime . " mins ago";
		}
	} else if($ctime<86400) {
		$ctime = floor($ctime/60/60);
		if($ctime==1) {
			 $ctime = "1 hour ago";
		} else {
			 $ctime = $ctime . " hours ago";
		}
	} else {
		$ctime = floor($ctime/86400);
		if($ctime==1) {
			 $ctime = "1 day ago";
		} else {
			 $ctime = $ctime . " days ago";
		}
	}
?>
<div class="item">
	<div class="fb-thumb"><img src="<?php echo $rows[$i]["fb_userpic"];?>"></div>
	<div class="fb-info">
		<div class="fb-username"><a href="https://facebook.com/<?php echo $rows[$i]["fb_userid"];?>" target="_blank"><?php echo $rows[$i]["fb_username"];?></a></div>
		<div class="fb-time"><?php echo $ctime;?></div>
	</div>
	<div class="clearfix"></div>
	<div class="item-word">
		<p><?php echo $rows[$i]["message"];?></p>
		<span><a data-toggle="modal" data-target="#PostModal" href="#" onclick="javascript:fn_modalShowMsg('<?php echo $rows[$i]["id"];?>');">更多</a></span>
	</div>
<?php
	if(file_exists(BASEPATH . "../uploads/fbactivity/" . $rows[$i]["id"] . ".jpg")) {
?>
<div class="item-photo"><img src="<?php echo base_url("fbactivity/photo/" . $rows[$i]["id"]);?>"></div>
<?php
	}
?>
	
	<div class="item-action">
		<span><a href="#" onclick="return fn_CallFbLike('<?php echo $rows[$i]["id"];?>');">讚</a></span>·
		<span><a href="javascript:fn_CallFbShare('<?php echo $rows[$i]["id"];?>');">分享</a></span>·
		<span>
			<a data-toggle="modal" data-target="#PostModal" href="#" onclick="javascript:fn_modalShowMsg('<?php echo $rows[$i]["id"];?>');">
				留言(
<span class="fb-comments-count" data-href="<?php echo base_url("fbactivity/article/p" . $rows[$i]["id"] . ".html");?>">0</span>
				)
			</a>
		</span>·
		<span>
			<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
			<span class="UFIBlingBoxText" id="like-text-<?php echo $rows[$i]["id"];?>"><?php echo $rows[$i]["like_num"];?></span>
		</span>
		<span>
			<i class="fa fa-share-alt" aria-hidden="true"></i>
			<span class="UFIBlingBoxText" id="share-text-<?php echo $rows[$i]["id"];?>"><?php echo $rows[$i]["share_num"];?></span>
		</span>
	</div>
</div>
<?php
}
?>

<div class="modal fade" id="PostModal" tabindex="-1" role="dialog" aria-labelledby="PostModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="modal-close">
          <a href="javascript:void(0);" class="close fb-close" data-dismiss="modal" aria-label="Close">關閉</a>
        </div>
        <div class="post-content">
<div class="item" id="modalMsgBox">
	<div class="fb-thumb"><img src=""></div>
	<div class="fb-info">
		<div class="fb-username"><a href="" target="_blank"></a></div>
		<div class="fb-time"></div>
	</div>
	<div class="clearfix"></div>
	<div class="item-word">
	</div>
	<div class="item-photo"></div>
	<div class="item-action">
		<span><a id="like_link" href="javascript:fn_CallFbLike('8');">讚</a></span>·
		<span><a id="share_link" href="javascript:fn_CallFbShare('8');">分享</a></span>·
		<span>
			<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
			<span class="UFIBlingBoxText" id="like-text-modal">0</span>
		</span>
		<span>
			<i class="fa fa-share-alt" aria-hidden="true"></i>
			<span class="UFIBlingBoxText" id="share-text-modal">0</span>
		</span>
	</div>
</div>
<div id="comment_box">
</div>
</div>

