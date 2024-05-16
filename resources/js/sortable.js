/**
 * Images rows can be sorted by mouse drop and down.
 * The new order of the images remains even after the page is reloaded.
 * This is useful if you want to get some order for your uploaded images, for example, for a slider.
 */
import Sortable from 'sortablejs';

document.addEventListener("DOMContentLoaded", () => {
  let el = document.querySelector('#upload ul.table');
  if (el) {
    let sortable = Sortable.create(el, {
      onEnd: function (evt) {
        axios.put('/api/images/' + evt.item.id, {
          oldIndex: evt.oldIndex,
          newIndex: evt.newIndex,
        })
        .then(response => {
          console.log(response);
        })
        .catch(err => {
          console.log(err);
        });
      },    
    });
  }
});
