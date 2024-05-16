/**
 * SimpleUpload.js plugin handler.
 * @see http://simpleupload.michaelcbrook.com/
 * @author Sergey Morozov <sergmoro1@ya.ru>
 */
$(document).ready(function () {
  $('input#file_input').change(function () {
      $(this).simpleUpload('/api/images', {

      allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
      maxFileSize: 0,
      data: uploadOptions.data,
      expect: 'json',

      start: function (file) {
        // add new line
        this.li = $('<li class="upload-table-li"></li>');
        // add thumbnail block
        this.thumbnail = $('<span class="thumbnail"></span>');
        // add progressbar to a block
        this.progressBar = $('<span class="progressBar"></span>');
        this.li.append(this.thumbnail.append(this.progressBar));
        // add line to table
        let table = $('#upload ul.table');
        table.append(this.li);
        // clear prev errors
        table.find('li.error').remove();
      },

      progress: function (progress) {
        this.progressBar.width(progress + "%");
      },

      success: function (data) {
        // Add new image line with addons fields
        this.progressBar.remove();
        if (data.success) {
          // set line id
          this.li.attr('id', data.file.id);
          // add image
          let img = $('<img class="thumbnail-img"/>').attr('src', data.file.thumb).attr('data-img', data.file.url);
          this.thumbnail.append(img);
          this.thumbnail.append(uploadOptions.image.tools);
          // add new line
          this.thumbnail.after(uploadOptions.image.line);
          // add buttons
          let buttons = $('<span class="buttons"></span>');
          buttons.append(uploadOptions.image.buttons);
          this.li.append(buttons);
        } else {
          // and message
          let message = $('<span/>').addClass('message').text(data.message);
          // add line with error to the table
          this.li.addClass('error').prepend(message);
        }
      },

      error: function (error) {
        this.progressBar.remove();
        let message = $('<span/>').addClass('message').text(error.message);
        this.li.addClass('error').prepend(message);
      }
    });
  });
});
