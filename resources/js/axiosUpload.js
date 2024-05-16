/**
 * axiosUpload.js multi file uploader
 * @author Sergey Morozov <sergmoro1@ya.ru>
 */
document.addEventListener("DOMContentLoaded", () => {

  const fileInput = document.querySelector('input#file_input');
  let table = document.querySelector('#upload ul.table');

  const imageUpload = async function (data) {
    const response = await axios.post('/api/images', data, {
      onUploadProgress: function (event) {
        progressBar.style.width = (Math.round(event.loaded / event.total) * 100) + '%';
      },
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    return response.data;
  };

  fileInput.addEventListener('change', (event) => {
    Array.from(fileInput.files).forEach((file) => {
      let formData = new FormData();
      formData.append('file_input', file);
      formData.append('imageable_type', uploadOptions.data.imageable_type);
      formData.append('imageable_id', uploadOptions.data.imageable_id);
      formData.append('limit', uploadOptions.data.limit);

      // create a new line
      let li = document.createElement('li');
      li.className = 'upload-table-li';
      // create progressbar
      let progressBar = document.createElement('span');
      progressBar.className = 'progressBar';
      progressBar.classList.add('thumbnail-img');
      // add progress bar
      li.append(progressBar);
      // add line to the table
      table.append(li);

      // clear prev errors
      let error = table.querySelector('li.error');
      if (error) {
        error.remove();
      };

      imageUpload(formData).then(function (data) {
        // progressBar no more needed
        progressBar.remove();
        if (data.success) {
          // Add new image line with addons fields
          
          // set line id
          li.setAttribute('id', data.file.id);
          // add thumbnail block
          let thumbnail = document.createElement('span'); 
          thumbnail.className = 'thumbnail';
          // add image
          let img = document.createElement('img');
          img.className = 'thumbnail-img';
          img.setAttribute('src', data.file.thumb)
          img.setAttribute('data-img', data.file.url);
          thumbnail.append(img);
          thumbnail.insertAdjacentHTML('beforeend', uploadOptions.image.tools);
          li.append(thumbnail);
          // add new line
          li.insertAdjacentHTML('beforeend', uploadOptions.image.line);
          // add buttons
          let buttons = document.createElement('span');
          buttons.className = 'buttons';
          buttons.innerHTML = uploadOptions.image.buttons;
          li.append(buttons);
        
        } else {
          // Error
        
          // and message
          let message = document.createElement('span');
          message.className = 'message';
          message.innerHTML = data.message;
          // add line with error to the table
          li.classList.add('error');
          li.prepend(message);
        }
      }).catch(function (error) {
        console.log(error);
      });
    });
  });

});
