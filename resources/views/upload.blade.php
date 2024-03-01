<div class='container'>
  <div class="max-w px-6 pt-6 bg-white border border-gray-100 rounded">
    <label class='label' for='file_input'>{{ __('imageable::messages.images') }}</label></p>
    <label class='button blue fileinput-button' for='file_input'>
      {{ __('imageable::messages.choose_a_file') }}
      <input class='hidden' type='file' name='file_input' id='file_input' multiple>
    </label>
    <div id='upload'>
      <ul class='table mt-5 mx-0'>
        @foreach ($model->images as $image)
          <li id='{{$image->id}}' class='upload-table-li'>
            <span class='thumbnail'>
              <img class='thumbnail-img' src='{{$image->getThumbnailUrl()}}' data-img='{{$image->getUrl()}}'>
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
<x-slot name="scripts">
  <script>var uploadOptions = <?= $model->uploadOptions($limit) ?>;</script>
</x-slot>
