<div id="profile_content">
<div id="status"></div>

<form class="form-horizontal" action="<?php echo base_url("fbactivity/post");?>" role="form" name="UploadDataFrm" id="UploadDataFrm" enctype="multipart/form-data" method="POST">
<input type="hidden" name="fbUsername" id="fbUsername" value="">
<input type="hidden" name="fbUserID" id="fbUserID" value="">
<input type="hidden" name="fbEmail" id="fbEmail" value="">
<input type="hidden" name="fbPicture" id="fbPicture" value="">

<div class="input-block">
<div class="photo-box"><img id="userphoto" src="<?php echo $user_img; ?>"><span style="font-size: 24px;"><?php echo $user_name; ?></span></div>
<div class="text-box">
<div class="text-caption"><h1>嗨! 請留言</h1><span class="fb-name-tmp"></span></div>

<div class="text-input-box">
<div class="input-photo">上傳圖片：
<input type="file" class="file" name="dPhoto" id="frm-dPhoto" accept=".jpg" multiple="">
</div><br>
<div class="input-area">
<textarea class="comment" name="dComment" id="frm-Comment" placeholder="在想些什麼"></textarea>
</div>
<div class="clearfix"></div>
</div>
<br>
<div class="input-submit">
<button value="1" class="btn-submit" type="submit">發佈</button>
</div>

</div>
<div class="clearfix"></div>
</div>

</form>

</div>
<hr>
