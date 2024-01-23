<div class='card'>
  <div class="card-content">
    <div class='field'>
      <label class='label' for='file_input'>{{ __('imageable::messages.images') }}</label>
      <div class='control'>
        <label class='button blue fileinput-button' for='file_input'>
          {{ __('imageable::messages.choose_a_file') }}
          <input class='input' type='file' name='file_input' id='file_input' multiple>
        </label>
      </div>
    </div>
  </div>
</div>
<div class='card'>
  <div class="card-content">
    <div id='uploads'>
      <ul class='table'>
        @foreach ($model->images as $image)
          <li id='{{$image->id}}'>
            <span class='block'>
              <img src='{{$image->getThumbnailUrl()}}' data-img='{{$image->getUrl()}}'>
              @include('vendor.imageable.line.tools')
            </span>
            
            @include('vendor.imageable.line.' . $model->getAddonFieldsView(), [
              'id' => $image->id, 
              'addons' => json_decode($image->addons)
            ])

            <span class='buttons'>
              @include('vendor.imageable.line.buttons')
            </span>
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
