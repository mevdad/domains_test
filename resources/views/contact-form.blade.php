<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Контактна форма</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            padding: 24px;
        }

        .card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255,255,255,0.1);
        }

        .card-header { text-align: center; margin-bottom: 36px; }

        .card-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .card-icon svg {
            width: 28px;
            height: 28px;
            fill: none;
            stroke: #fff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        h1 { font-size: 1.625rem; font-weight: 700; color: #1a1a2e; letter-spacing: -0.3px; }

        .subtitle { margin-top: 6px; font-size: 0.9rem; color: #6b7280; }

        /* Alert banners */
        .alert {
            border-radius: 10px;
            padding: 0 16px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            margin-bottom: 0;
            transition: max-height 0.35s ease, opacity 0.3s ease, padding 0.35s ease, margin-bottom 0.35s ease;
        }
        .alert.show { max-height: 100px; opacity: 1; padding: 14px 16px; margin-bottom: 24px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        .alert-success::before {
            content: '✓';
            font-size: 1rem;
            font-weight: 700;
            flex-shrink: 0;
            background: #16a34a;
            color: #fff;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .alert-error::before {
            content: '!';
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
            background: #ef4444;
            color: #fff;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Fields */
        .field { margin-bottom: 20px; }

        label { display: block; font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 6px; letter-spacing: 0.01em; }
        label .optional { font-weight: 400; color: #9ca3af; font-size: 0.8rem; margin-left: 4px; }
        label .required { color: #ef4444; margin-left: 2px; }

        input:not([type="submit"]),
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
            color: #111827;
            background: #fafafa;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            outline: none;
            appearance: none;
        }

        input:not([type="submit"]):focus,
        textarea:focus {
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }

        input.is-invalid,
        textarea.is-invalid { border-color: #ef4444; background: #fff8f8; }

        input.is-invalid:focus,
        textarea.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12); }

        textarea { height: 120px; resize: vertical; min-height: 90px; }

        .error-msg {
            display: flex;
            align-items: center;
            gap: 5px;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            margin-top: 0;
            font-size: 0.8rem;
            color: #dc2626;
            font-weight: 500;
            transition: max-height 0.25s ease, opacity 0.2s ease, margin-top 0.25s ease;
        }
        .error-msg.show { max-height: 40px; opacity: 1; margin-top: 6px; }
        .error-msg::before {
            content: '!';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 15px;
            height: 15px;
            background: #ef4444;
            color: #fff;
            border-radius: 50%;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* Button */
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 8px;
            transition: opacity 0.18s, transform 0.12s, box-shadow 0.18s;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        }
        .btn:hover:not(:disabled) { opacity: 0.92; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5); transform: translateY(-1px); }
        .btn:active:not(:disabled) { transform: translateY(0); opacity: 1; }
        .btn:disabled { opacity: 0.65; cursor: not-allowed; }

        .btn-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: middle;
            margin-right: 8px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 480px) {
            body {
                padding: 0;
                align-items: flex-start;
            }
            .card {
                border-radius: 0;
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div class="card-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <h1>Зв'яжіться з нами</h1>
            <p class="subtitle">Заповніть форму і ми відповімо якнайшвидше</p>
        </div>

        <div id="form-alert" class="alert"></div>

        <form id="contact-form" method="POST" action="{{ route('contact-form.submit') }}" novalidate>
            @csrf

            <div class="field">
                <label for="name">
                    Ім'я <span class="optional">(необов'язково)</span>
                </label>
                <input type="text" id="name" name="name" placeholder="Ваше ім'я" autocomplete="name">
                <div class="error-msg"></div>
            </div>

            <div class="field">
                <label for="email">
                    Email <span class="required">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="your@email.com"
                    autocomplete="email"
                    required
                    data-error-required="Поле Email є обов'язковим для заповнення."
                    data-error-email="Вкажіть коректну адресу електронної пошти."
                >
                <div class="error-msg"></div>
            </div>

            <div class="field">
                <label for="message">
                    Повідомлення <span class="optional">(необов'язково)</span>
                </label>
                <textarea id="message" name="message" placeholder="Ваше повідомлення..."></textarea>
                <div class="error-msg"></div>
            </div>

            <button type="submit" class="btn">Надіслати повідомлення</button>
        </form>
    </div>

    <script>
        class FieldValidator {
            static EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            static validate(input) {
                const value = input.value.trim();

                if (input.required && !value) {
                    return input.dataset.errorRequired ?? 'Це поле є обов\'язковим для заповнення.';
                }

                if (input.type === 'email' && value && !this.EMAIL_RE.test(value)) {
                    return input.dataset.errorEmail ?? 'Вкажіть коректну адресу електронної пошти.';
                }

                if (input.maxLength > 0 && value.length > input.maxLength) {
                    return (input.dataset.errorMaxlength ?? 'Максимум :max символів.')
                        .replace(':max', input.maxLength);
                }

                return null;
            }
        }

        class AjaxForm {
            constructor(formEl) {
                this.form      = formEl;
                this.alertEl   = document.getElementById('form-alert');
                this.submitBtn = formEl.querySelector('[type="submit"]');
                this.submitBtn.dataset.label = this.submitBtn.textContent.trim();
                this._bindEvents();
            }

            _fields() {
                return [...this.form.querySelectorAll(
                    'input:not([type="hidden"]):not([type="submit"]), textarea, select'
                )];
            }

            _errorEl(input) {
                return input.closest('.field')?.querySelector('.error-msg') ?? null;
            }

            _showFieldError(input, msg) {
                const el = this._errorEl(input);
                if (el) { el.textContent = msg; el.classList.add('show'); }
                input.classList.add('is-invalid');
            }

            _clearFieldError(input) {
                const el = this._errorEl(input);
                if (el) { el.textContent = ''; el.classList.remove('show'); }
                input.classList.remove('is-invalid');
            }

            _clearAll() {
                this._fields().forEach(f => this._clearFieldError(f));
                this.alertEl.textContent = '';
                this.alertEl.className = 'alert';
            }

            _showAlert(msg, type = 'success') {
                this.alertEl.textContent = msg;
                this.alertEl.className = `alert alert-${type} show`;
            }

            _setLoading(on) {
                this.submitBtn.disabled = on;
                this.submitBtn.innerHTML = on
                    ? '<span class="btn-spinner"></span>Надсилання...'
                    : this.submitBtn.dataset.label;
            }

            _validateAll() {
                let valid = true;
                for (const field of this._fields()) {
                    const err = FieldValidator.validate(field);
                    if (err) { this._showFieldError(field, err); valid = false; }
                }
                return valid;
            }

            _bindEvents() {
                this._fields().forEach(field => {
                    field.addEventListener('input', () => this._clearFieldError(field));
                });

                this.form.addEventListener('submit', e => this._handleSubmit(e));
            }

            async _handleSubmit(e) {
                e.preventDefault();
                this._clearAll();

                if (!this._validateAll()) { return; }

                this._setLoading(true);

                try {
                    const res = await fetch(this.form.action, {
                        method: (this.form.method || 'POST').toUpperCase(),
                        headers: {
                            'X-CSRF-TOKEN': this.form.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        },
                        body: new FormData(this.form),
                    });

                    const data = await res.json();

                    if (res.ok) {
                        this._showAlert(data.message);
                        this.form.reset();
                    } else if (res.status === 422 && data.errors) {
                        for (const [name, msgs] of Object.entries(data.errors)) {
                            const field = this.form.querySelector(`[name="${name}"]`);
                            if (field) { this._showFieldError(field, msgs[0]); }
                        }
                    } else {
                        this._showAlert(data.message || 'Щось пішло не так. Спробуйте ще раз.', 'error');
                    }
                } catch {
                    this._showAlert('Помилка мережі. Перевірте з\'єднання та спробуйте ще раз.', 'error');
                } finally {
                    this._setLoading(false);
                }
            }
        }

        new AjaxForm(document.getElementById('contact-form'));
    </script>
</body>
</html>
