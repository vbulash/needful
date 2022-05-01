<div class="row mb-4">
	@php
		$view = $show ?? false;
	@endphp
	<label class=" col-sm-3 col-form-label" for="user_id">@if($view)
			Данная анкета связана с записью пользователя
		@else
			Свяжите данную анкету практиканта с записью
			пользователя
		@endif
		@hasrole('Практикант') (только текущий пользователь) @endhasrole
		@hasrole('Работодатель') (только текущий пользователь) @endhasrole
		@if(!$show)
			<span class="required">*</span>
		@endif
	</label>
	<div class="col-sm-5 col-form-label">
		<select name="user_id" id="user_id" class="form-control select2" @if($view) disabled @endif>
			@hasrole('Администратор')
			<option selected disabled>Выберите пользователя</option>
			@foreach($users as $user)
				<option value="{{ $user['id'] }}"
						@if(isset($student) && $student->user->getKey() == $user['id'])
							selected
						@elseif(auth()->user()->getKey() == $user['id'])
							selected
					@endif
				>
					{{ $user['name'] }}</option>
			@endforeach
			@endhasrole

			@hasrole('Работодатель')
			<option value="{{ auth()->user()->getKey() }}">
				{{ auth()->user()->name }}
			</option>
			@endhasrole

			@hasrole('Практикант')
			<option value="{{ auth()->user()->getKey() }}">
				{{ auth()->user()->name }}
			</option>
			@endhasrole
		</select>
	</div>
</div>
