@component('mail::message')
# Your post has been just commented.

{{$sender}} commented your post : {{$text}}

@component('mail::button', ['url' => ''])
Go to the post 
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
