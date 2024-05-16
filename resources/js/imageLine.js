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
