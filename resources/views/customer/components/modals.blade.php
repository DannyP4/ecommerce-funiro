<div class="modal-overlay" id="{{ $modalId }}" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4>{{ __($title) }}</h4>
        </div>
        <div class="modal-body">
            <p>{{ __($message) }}</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('{{ $modalId }}')">{{ __($cancelText ?? 'Cancel') }}</button>
            <button type="button" class="btn-danger" id="{{ $modalId }}_confirm">{{ __($confirmText ?? 'Confirm') }}</button>
        </div>
    </div>
</div>

@push('styles')
<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.modal-content {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal-header {
    padding: 20px 25px 0;
}

.modal-header h4 {
    margin: 0;
    color: #333;
    font-size: 18px;
}

.modal-body {
    padding: 15px 25px;
    color: #666;
    line-height: 1.5;
}

.modal-footer {
    padding: 0 25px 20px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-danger {
    background: #dc3545;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-secondary {
    background: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-secondary:hover {
    background: #5a6268;
}
</style>
@endpush

@push('scripts')
<script>
window.closeModal = function(modalId) {
    document.getElementById(modalId).style.display = 'none';
};

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('{{ $modalId }}');
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal('{{ $modalId }}');
            }
        });
    }
});
</script>
@endpush
