
<h1>TreLire</h1>
<div class="testbox">
<form method="post">
<table width="100%">
  <tr>
<td>
<select name="content-type" class="select-css">
    <option value="text/html">text/html</option>
    <option value="text/plain">text/plain</option>
</select>
</td>
<td>
<input type="text" name="to" class="Input-text" placeholder="To (separated by comma)" required>
</td>
</tr>
<tr>
<td>
<input type="text" name="from" placeholder="From" required>
</td>
<td>
<input type="text" name="reply-to" placeholder="Reply to">
</td>
</tr>
<tr>
  <td>
<input type="text" name="cc" placeholder="CC (separated by comma)">
</td>
<td>
<input type="text" name="bcc" placeholder="BCC (separated by comma)">
</td>
</tr>
<tr>
  <td>
<input type="text" name="subject" placeholder="Subject"><br>
</td>
<td>
<input id="attachment-ids" type="hidden" name="attachment" />
<input id="upload-button" type="button" class="treLireButtons" value="Select attachments" />
</td>
</tr>
</table>


<?php wp_editor('', 'mail_content');?>
<input type="submit" value="Submit" class="treLireButton">
</form>
</div>
<script>
jQuery(document).ready(function($){

var mediaUploader;

$('#upload-button').click(function(e) {
  e.preventDefault();
  // If the uploader object has already been created, reopen the dialog
    if (mediaUploader) {
    mediaUploader.open();
    return;
  }
  // Extend the wp.media object
  mediaUploader = wp.media.frames.file_frame = wp.media({
    title: 'Choose attachments',
    button: {
    text: 'Choose attachments'
  }, multiple: 'add' });

  // When a file is selected, grab the URL and set it as the text field's value
  mediaUploader.on('select', function(){
    var selection = mediaUploader.state().get('selection');
    selection.map( function( attachment ) {
      attachment = attachment.toJSON();
          if ($('#attachment-ids').val() === '')
            $('#attachment-ids').val(attachment.id);
          else
            $('#attachment-ids').val($('#attachment-ids').val() + ',' + attachment.id);
    });
    console.log($('#attachment-ids').val())
  });
  mediaUploader.on('open',function() {
  var selection = mediaUploader.state().get('selection');
  var ids_value = jQuery('#attachment-ids').val();

  if(ids_value.length > 0) {
    var ids = ids_value.split(',');

    ids.forEach(function(id) {
      attachment = wp.media.attachment(id);
      attachment.fetch();
      selection.add(attachment ? [attachment] : []);
    });
  }
});
  // Open the uploader dialog
  mediaUploader.open();
});

});
</script>