{{--
  @var object $addons addons values
  or
  @var array $defaults image default addons values
--}}
<span class="line">
  <input class="rounded-md p-2" name="year" placeholder="{{__('imageable::messages.year')}}" type="text" readonly 
    value="<?= isset($addons) ? $addons->year : $defaults['year'] ?>" />
  <select class="rounded-md p-2" name="category" readonly>
    <option value='home' <?= isset($addons) ? ($addons->category == 'home' ? 'selected' : '') : 'selected' ?>>
      {{__('imageable::messages.home')}}
    </option>
    <option value='office' <?= isset($addons) ? ($addons->category == 'office' ? 'selected' : '') : '' ?>>
    {{__('imageable::messages.office')}}
    </option>
    <option value='street' <?= isset($addons) ? ($addons->category == 'street' ? 'selected' : '') : '' ?>>
    {{__('imageable::messages.street')}}
    </option>
  </select>
  <textarea class="resize-y rounded-md p-2" name="caption" placeholder="{{__('imageable::messages.caption')}}"
    readonly><?= isset($addons) ? $addons->caption : $defaults['caption'] ?></textarea>
</span>