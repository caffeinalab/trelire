<h1>TreLire</h1>
<form method="post">
<h3>Content-type</h3>
<select name="content-type">
    <option value="text/html">text/html</option>
    <option value="text/plain">text/plain</option>
</select>
<h3>TO:</h3>
<h5>(separated by comma)</h5>
<input type="text" name="to" required>
<h3>FROM:</h3>
<input type="text" name="from" required>
<h3>REPLY TO:</h3>
<input type="text" name="reply-to">
<h3>CC:</h3>
<h5>(separated by comma)</h5>
<input type="text" name="cc">
<h3>BCC:</h3>
<h5>(separated by comma)</h5>
<input type="text" name="bcc">
<h3>Subject:</h3>
<input type="text" name="subject"><br>
<input id="attachment-ids" type="hidden" name="attachment" />
<input id="upload-button" type="button" class="button" value="Select attachments" />
<?php wp_editor('', 'mail_content');?>
<input type="submit" value="Submit">
</form>

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