<a href="javascript:;">
    <button  class="button small green" id="btn-edit" type="button"
        title="{{ __('imageable::messages.edit_image') }}"
        onclick="imageLine.edit(this);">
        <span class="material-icons">edit</span>
    </button>
</a>
<a href="javascript:;">
    <button  class="button small red" id="btn-delete" type="button"
        title="{{ __('imageable::messages.delete_image') }}"
        onclick="imageLine.delete(this);">
        <span class="material-icons">delete</span>
    </button>
</a>
<a href="javascript:;">
    <button class="button small blue inactive" id="btn-save" type="button"
        title="{{ __('imageable::messages.save_image') }}"
        onclick="imageLine.save(this);">
        <span class="material-icons">save</span>
    </button>
</a>
<a href="javascript:;">
    <button class="button small inactive" id="btn-cancel" type="button"
        title="{{ __('imageable::messages.undo_editing') }}"
        onclick="imageLine.cancel(this);">
        <span class="material-icons">cancel</span>
    </button>
</a>
