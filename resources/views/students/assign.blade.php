@php
	$special = \App\Models\User::special();
@endphp
<div class="row mb-4">
	<label class=" col-sm-3 col-form-label" for="user_id">
		@if($mode == config('global.show'))
			Данная анкета связана с записью пользователя
		@else
			Свяжите данную анкету учащегося с записью
			пользователя
		@endif
		@hasrole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value) (только текущий пользователь) @endhasrole
		@hasrole(\App\Http\Controllers\Auth\RoleName::EMPLOYER->value) (только текущий пользователь) @endhasrole
		@if($mode != config('global.show'))
			<span class="required">*</span>
		@endif
	</label>
	<div class="col-sm-5 col-form-label">
		<select name="user_id" id="user_id" class="form-control select2"
				@if($mode == config('global.show')) disabled @endif
		>
			@hasrole(\App\Http\Controllers\Auth\RoleName::ADMIN->value)
			@foreach($users as $user)
				<option value="{{ $user['id'] }}"
				@if(isset($student))
					@if ($student->user->getKey() == $user['id']))
						selected
					@endif
				@elseif ($special->getKey() == $user['id'])
{{--					if(auth()->user()->getKey() == $user['id'])--}}
					selected
				@endif
				>
					{{ $user['name'] }}</option>
			@endforeach
			@endhasrole

			@hasrole(\App\Http\Controllers\Auth\RoleName::EMPLOYER->value)
			<option value="{{ auth()->user()->getKey() }}">
				{{ auth()->user()->name }}
			</option>
			@endhasrole

			@hasrole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value)
			<option value="{{ auth()->user()->getKey() }}">
				{{ auth()->user()->name }}
			</option>
			@endhasrole
		</select>
	</div>
</div>
