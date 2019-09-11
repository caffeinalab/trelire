<h1>TreLire</h1>
<form method="post">
<h3>Content-type</h3>
<select name="content-type">
    <option value="text/html">text/html</option>
    <option value="text/plain">text/plain</option>
</select>
<h3>TO:</h3>
<h5>(separated by comma)</h5>
<input type="text" name="to">
<h3>FROM:</h3>
<input type="text" name="from">
<h3>REPLY TO:</h3>
<input type="text" name="reply-to">
<h3>CC:</h3>
<h5>(separated by comma)</h5>
<input type="text" name="cc">
<h3>BCC:</h3>
<h5>(separated by comma)</h5>
<input type="text" name="bcc">
<h3>Attachment paths:</h3>
<h5>(separated by comma)</h5>
<input type="text" name="attachments">
<h3>Subject:</h3>
<input type="text" name="subject"><br>
<?php wp_editor('', 'mail_content');?>
<input type="submit" value="Submit">
</form>