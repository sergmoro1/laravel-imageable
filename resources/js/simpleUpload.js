/**
 * Copying text to the clipboard for later use in the text area.
 */
async function copyTextToClipboard(text) {
  try {
      await navigator.clipboard.writeText(text);
  } catch (err) {
      console.error('Error in copying text: ', err);
  }
}

/**
 * Image line actions handler.
 * Image line - line with additional fields as a caption for image and action buttons: edit, delete, save and cancel.
 * @author Sergey Morozov <sergmoro1@ya.ru>
 */
window.imageLine = {
  // delete image from DB, all related files from the disk and delete imaqe line
  delete: function(that) {
    this.li = that.closest('li');
    this.id = this.li.getAttribute('id');

    axios.delete('/api/images/' + this.id)
    .then(response => {
      this.li.remove();
    })
    .catch(error => {
      console.log(err);
    });
  },
  // open fields in image line for edition
  edit: function(that) {
    this.li = that.closest('li');
    uploadOptions.fields.forEach((field) => {
        this.li.querySelector("[name='" + field + "']").removeAttribute('readonly');
    });
    this.buttonsSwitch();
  },
  // save edition results 
  save: function(that) {
    this.li = that.closest('li');
    this.id = this.li.getAttribute('id');

    let data = [];
    let span = this.li.getElementsByClassName('fields');
    for (let inpt of span[0].getElementsByTagName('input')) {
      if (inpt.type == 'text') {
        data[inpt.name] = inpt.value;
      }
    }
    for (let tag of ['select', 'textarea']) {
      for (let fld of span[0].getElementsByTagName(tag)) {
        data[fld.name] = fld.value;
      }
    }
    
    axios.put('/api/images/' + this.id, {
      addons: JSON.stringify({ ...data })
    })
    .then(response => {
      this.buttonsSwitch(true);
    })
    .catch(err => {
      console.log(err);
    });
  },
  // cancel edition
  cancel: function(that) {
    this.li = that.closest('li');
    this.buttonsSwitch(true);
  },
  // copy image link to clipboard
  copy: function(that) {
    let li = that.closest('li');

    let img = li.querySelector('.thumbnail > img.thumbnail-img');
    let imgLink = new URL(img.getAttribute('data-img'), window.location.href);
    
    copyTextToClipboard(imgLink);
  },
  // make active buttons inactive and vice versa
  // add to image fields readonly attribute or remove it
  buttonsSwitch: function(readonly = false) {
    let span = this.li.getElementsByClassName('buttons');
    uploadOptions.fields.forEach((name) => {
      let field = this.li.querySelector("[name='" + name + "']");
      if (readonly) {
        field.setAttribute('readonly','readonly');
      } else {
        field.removeAttribute('readonly');
      }
    });
    for (let btn of span[0].getElementsByTagName('button')) {
      btn.classList.toggle('hidden');
    }
  },
};

/**
 * SimpleUpload.js handler.
 * @see http://simpleupload.michaelcbrook.com/
 */
$(document).ready(function () {
  $('#file_input').change(function () {
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
