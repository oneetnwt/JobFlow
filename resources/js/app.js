import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const APP_CONFIRM_DIALOG_ID = 'app-confirm-dialog';

function ensureConfirmDialog() {
	let dialog = document.getElementById(APP_CONFIRM_DIALOG_ID);

	if (dialog) {
		return dialog;
	}

	dialog = document.createElement('dialog');
	dialog.id = APP_CONFIRM_DIALOG_ID;
	dialog.setAttribute('aria-label', 'Confirmation dialog');
	dialog.className = 'app-confirm-dialog';
	dialog.innerHTML = `
		<form method="dialog" class="app-confirm-card">
			<p class="app-confirm-title">Please Confirm</p>
			<p class="app-confirm-message"></p>
			<div class="app-confirm-actions">
				<button value="cancel" class="app-confirm-cancel" type="submit">Cancel</button>
				<button value="confirm" class="app-confirm-ok" type="submit">Continue</button>
			</div>
		</form>
	`;

	const style = document.createElement('style');
	style.textContent = `
		dialog.app-confirm-dialog {
			position: fixed;
			inset: 0;
			margin: auto;
			border: 0;
			padding: 0;
			background: transparent;
			max-width: 92vw;
		}
		dialog.app-confirm-dialog::backdrop {
			background: rgba(0, 0, 0, 0.68);
			backdrop-filter: blur(2px);
		}
		.app-confirm-card {
			width: min(480px, 92vw);
			border: 1px solid var(--color-border-strong, #9a958d);
			background: var(--color-surface, #ece9e4);
			color: var(--color-text-primary, #1a1814);
			padding: 20px;
			display: grid;
			gap: 14px;
		}
		.app-confirm-title {
			margin: 0;
			font-weight: 700;
			letter-spacing: 0.06em;
			text-transform: uppercase;
			font-size: 12px;
		}
		.app-confirm-message {
			margin: 0;
			font-size: 14px;
			line-height: 1.5;
		}
		.app-confirm-actions {
			display: flex;
			justify-content: flex-end;
			gap: 10px;
			margin-top: 4px;
		}
		.app-confirm-actions button {
			border: 1px solid var(--color-border, #c8c4bc);
			background: var(--color-bg-subtle, #efefeb);
			color: var(--color-text-primary, #1a1814);
			padding: 8px 14px;
			font-size: 12px;
			font-weight: 700;
			letter-spacing: 0.06em;
			text-transform: uppercase;
			cursor: pointer;
		}
		.app-confirm-actions .app-confirm-ok {
			background: var(--color-accent, #c0440e);
			color: #fff;
			border-color: var(--color-accent, #c0440e);
		}
	`;

	document.head.appendChild(style);
	document.body.appendChild(dialog);

	return dialog;
}

function showConfirmDialog(message) {
	const dialog = ensureConfirmDialog();
	const messageNode = dialog.querySelector('.app-confirm-message');

	if (messageNode) {
		messageNode.textContent = message;
	}

	return new Promise((resolve) => {
		const onClose = () => {
			dialog.removeEventListener('close', onClose);
			resolve(dialog.returnValue === 'confirm');
		};

		dialog.addEventListener('close', onClose, { once: true });
		dialog.showModal();
	});
}

window.appConfirm = showConfirmDialog;

document.addEventListener('submit', async (event) => {
	const form = event.target;

	if (!(form instanceof HTMLFormElement)) {
		return;
	}

	const message = form.getAttribute('data-confirm');

	if (!message) {
		return;
	}

	if (form.dataset.confirmed === '1') {
		delete form.dataset.confirmed;
		return;
	}

	event.preventDefault();

	const confirmed = await showConfirmDialog(message);

	if (!confirmed) {
		return;
	}

	form.dataset.confirmed = '1';

	if (typeof form.requestSubmit === 'function') {
		form.requestSubmit();
	} else {
		form.submit();
	}
});
