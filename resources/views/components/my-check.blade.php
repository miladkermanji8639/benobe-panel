<div class="checkbox-wrapper-1">
    <div class="d-flex ">
        <input {{ $isChecked ? 'checked' : '' }} id="{{ $id }}" class="substituted" type="checkbox" aria-hidden="true" />
        <label for="{{ $id }}" class="text-dark font-weight-bold">{{ $day }}</label>
    </div>
</div>



<style>
 .checkbox-wrapper-1 *,
 .checkbox-wrapper-1 ::after,
 .checkbox-wrapper-1 ::before {
  box-sizing: border-box;
 }

 .checkbox-wrapper-1 [type=checkbox].substituted {
  margin: 0;
  width: 0;
  height: 0;
  display: inline;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
 }

 .checkbox-wrapper-1 [type=checkbox].substituted+label:before {
  content: "";
  display: inline-block;
  vertical-align: middle;
  height: 1.15em;
  width: 1.15em;
  margin-left: 4px;
  color: rgba(0, 0, 0, 0.275);
  border: solid 0.06em;
  box-shadow: 0 0 0.04em, 0 0.06em 0.16em -0.03em inset, 0 0 0 0.07em transparent inset;
  border-radius: 0.2em;
  background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xml:space="preserve" fill="white" viewBox="0 0 9 9"><rect x="0" y="4.3" transform="matrix(-0.707 -0.7072 0.7072 -0.707 0.5891 10.4702)" width="4.3" height="1.6" /><rect x="2.2" y="2.9" transform="matrix(-0.7071 0.7071 -0.7071 -0.7071 12.1877 2.9833)" width="6.1" height="1.7" /></svg>') no-repeat center, white;
  background-size: 0;
  will-change: color, border, background, background-size, box-shadow;
  transform: translate3d(0, 0, 0);
  transition: color 0.1s, border 0.1s, background 0.15s, box-shadow 0.1s;
 }

 .checkbox-wrapper-1 [type=checkbox].substituted:enabled:active+label:before,
 .checkbox-wrapper-1 [type=checkbox].substituted:enabled+label:active:before {
  box-shadow: 0 0 0.04em, 0 0.06em 0.16em -0.03em transparent inset, 0 0 0 0.07em rgba(0, 0, 0, 0.1) inset;
  background-color: #f0f0f0;
 }

 .checkbox-wrapper-1 [type=checkbox].substituted:checked+label:before {
  background-color: #3B99FC;
  background-size: 0.75em;
  color: rgba(0, 0, 0, 0.075);
 }

 .checkbox-wrapper-1 [type=checkbox].substituted:checked:enabled:active+label:before,
 .checkbox-wrapper-1 [type=checkbox].substituted:checked:enabled+label:active:before {
  background-color: #0a7ffb;
  color: rgba(0, 0, 0, 0.275);
 }

 .checkbox-wrapper-1 [type=checkbox].substituted:focus+label:before {
  box-shadow: 0 0 0.04em, 0 0.06em 0.16em -0.03em transparent inset, 0 0 0 0.07em rgba(0, 0, 0, 0.1) inset, 0 0 0 3.3px rgba(65, 159, 255, 0.55), 0 0 0 5px rgba(65, 159, 255, 0.3);
 }

 .checkbox-wrapper-1 [type=checkbox].substituted:focus:active+label:before,
 .checkbox-wrapper-1 [type=checkbox].substituted:focus+label:active:before {
  box-shadow: 0 0 0.04em, 0 0.06em 0.16em -0.03em transparent inset, 0 0 0 0.07em rgba(0, 0, 0, 0.1) inset, 0 0 0 3.3px rgba(65, 159, 255, 0.55), 0 0 0 5px rgba(65, 159, 255, 0.3);
 }

 .checkbox-wrapper-1 [type=checkbox].substituted:disabled+label:before {
  opacity: 0.5;
 }

 .checkbox-wrapper-1 [type=checkbox].substituted.dark+label:before {
  color: rgba(255, 255, 255, 0.275);
  background-color: #222;
  background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xml:space="preserve" fill="rgba(34, 34, 34, 0.999)" viewBox="0 0 9 9"><rect x="0" y="4.3" transform="matrix(-0.707 -0.7072 0.7072 -0.707 0.5891 10.4702)" width="4.3" height="1.6" /><rect x="2.2" y="2.9" transform="matrix(-0.7071 0.7071 -0.7071 -0.7071 12.1877 2.9833)" width="6.1" height="1.7" /></svg>');
 }

 .checkbox-wrapper-1 [type=checkbox].substituted.dark:enabled:active+label:before,
 .checkbox-wrapper-1 [type=checkbox].substituted.dark:enabled+label:active:before {
  background-color: #444;
  box-shadow: 0 0 0.04em, 0 0.06em 0.16em -0.03em transparent inset, 0 0 0 0.07em rgba(255, 255, 255, 0.1) inset;
 }

 .checkbox-wrapper-1 [type=checkbox].substituted.dark:checked+label:before {
  background-color: #a97035;
  color: rgba(255, 255, 255, 0.075);
 }

 .checkbox-wrapper-1 [type=checkbox].substituted.dark:checked:enabled:active+label:before,
 .checkbox-wrapper-1 [type=checkbox].substituted.dark:checked:enabled+label:active:before {
  background-color: #c68035;
  color: rgba(0, 0, 0, 0.275);
 }

 .checkbox-wrapper-1 [type=checkbox].substituted+label {
  -webkit-user-select: none;
  user-select: none;
 }
</style>
