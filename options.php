<?php
function nyarukolive_options_page_init() {
	add_management_page('直播设置', 'wpNyaruko 直播选项', 'manage_options', NYARUKOLIVE_TEXT_DOMAIN.'-options', 'nyarukolive_options_page');
}
add_action('admin_menu', 'nyarukolive_options_page_init');
function nyarukolive_options_page() {
?>
<div id="optionbox">
	<div id="wpNyarukoOptionTitle"><a title="版本升级日志" class="link" href="https://github.com/kagurazakayashi/wpNyarukoLive/commits/master" target="_blank"><div id="wpNyarukoPanelLogo"></div></a>&nbsp;视频直播（版本&nbsp;0.1）</div><hr>
	<form action="#" method="post" enctype="multipart/form-data" name="op_form" id="op_form">
	<table border="0" cellspacing="0" cellpadding="10">
	<tbody>
	<tr>
        <td>笔记(不呈现)</td>
        <td><input name="wpNyarukoTest" type="text" id="wpNyarukoTest" value="" size=64 maxlength=128 /></td>
    </tr>
	</tbody>
	</table>
	<hr><p><input id="submitoption" type="submit" name="input_save" value="应用这些设定" /></p>
	</form>
</div>
<?php
}
?>