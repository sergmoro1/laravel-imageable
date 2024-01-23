{{--
  @var object $addons addons values
  or
  @var array $defaults image default addons values
--}}
<span class="line">
  <textarea class="textarea" name="caption" placeholder="{{__('imageable::messages.caption')}}" readonly><?= isset($addons) ? $addons->caption : $defaults['caption'] ?></textarea>
</span>