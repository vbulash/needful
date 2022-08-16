<div class="modal fade" id="modal-support" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="modal-support-label" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="support-title">Обращение к администратору платформы</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" id="support-body">
					<div class="form-floating mb-4">
						<textarea class="form-control" id="message" name="message" placeholder="Сообщение" style="height: 200px;" required></textarea>
						<label class="form-label" for="message">Текст обращения &gt;</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="support-yes" data-bs-dismiss="modal" disabled>Отправить</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
				</div>
		</div>
	</div>
</div>

@push('js_after')
	<script>
		document.getElementById('support-yes').addEventListener('click', (event) => {
			// let data = [
			// 	document.getElementById('subject').value,
			// 	document.getElementById('message').value
			// ];
			let csrf_token = document.querySelector("meta[name='csrf-token']").getAttribute('content');
			$.ajax({
				method: 'POST',
				url: "{{ route('support', ['sid' => session()->getId()]) }}",
				data: {
					message: $('#message').val()
				},
				headers: {'X-CSRF-TOKEN': csrf_token},
				success: (response) => {
					$('#message').val('');
					showToast('success', 'Письмо администратору платформы отправлено', false);
				},
				error: (response) => {
					$('#message').val('');
					const status = response.status;
					const message = response.responseJSON.message;
					showToast('error', 'Ошибка ' + status + ': ' + message, false);
				}
			});
		}, false);

		document.getElementById('message').addEventListener('input', (event) => {
			let text = event.target.value;
			document.getElementById('support-yes').disabled = (text.length === 0);
		}, false);
	</script>
@endpush
