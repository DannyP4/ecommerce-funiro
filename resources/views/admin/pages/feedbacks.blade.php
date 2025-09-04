@extends('admin.layouts.app')

@section('content')
<div class="content-section active">
    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">{{ __('Feedbacks Management') }}</h2>
        </div>
        <div style="padding: 20px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Rating') }}</th>
                        <th>{{ __('Comment') }}</th>
                        <th>{{ __('Review Date') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $feedback)
                    <tr>
                        <td>#{{ $feedback->feedback_id }}</td>
                        <td>{{ $feedback->user->name ?? __('N/A') }}</td>
                        <td>{{ $feedback->product->name ?? __('N/A') }}</td>
                        <td>
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $feedback->rating)
                                    <i class="fas fa-star" style="color: #ffc107;"></i>
                                @else
                                    <i class="far fa-star" style="color: #ccc;"></i>
                                @endif
                            @endfor
                        </td>
                        <td>{{ Str::limit($feedback->comment, 50) }}</td>
                        <td>{{ $feedback->created_at ? $feedback->created_at->format('d/m/Y') : __('N/A') }}</td>
                        <td>
                            <button class="btn btn-secondary btn-sm" onclick="viewFeedback({{ $feedback->feedback_id }})" title="{{ __('View Details') }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteFeedback({{ $feedback->feedback_id }})" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">{{ __('No feedbacks found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $feedbacks->links('pagination.pagination') }}
            </div>
        </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentFeedbackId = null;

    window.viewFeedback = function(feedbackId) {
        fetch(`{{ route('admin.feedbacks.show', ':id') }}`.replace(':id', feedbackId))
            .then(response => response.json())
            .then(data => {
                const feedback = data.feedback;
                const user = data.user;
                const product = data.product;
                
                document.getElementById('feedback-customer').textContent = user ? user.name : '{{ __("N/A") }}';
                document.getElementById('feedback-product').textContent = product ? product.name : '{{ __("N/A") }}';
                document.getElementById('feedback-comment').textContent = feedback.comment;
                document.getElementById('feedback-date').textContent = new Date(feedback.created_at).toLocaleDateString();
                
                let ratingHtml = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= feedback.rating) {
                        ratingHtml += '<i class="fas fa-star" style="color: #ffc107; margin-right: 2px;"></i>';
                    } else {
                        ratingHtml += '<i class="far fa-star" style="color: #ccc; margin-right: 2px;"></i>';
                    }
                }
                ratingHtml += ` (${feedback.rating}/5)`;
                document.getElementById('feedback-rating').innerHTML = ratingHtml;
                
                adminPanel.openModal('viewFeedbackModal');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("Error loading feedback details") }}');
            });
    };

    window.deleteFeedback = function(feedbackId) {
        currentFeedbackId = feedbackId;
        
        fetch(`{{ route('admin.feedbacks.show', ':id') }}`.replace(':id', feedbackId))
            .then(response => response.json())
            .then(data => {
                const feedback = data.feedback;
                const user = data.user;
                const product = data.product;
                
                let previewHtml = `
                    <strong>{{ __('Customer') }}:</strong> ${user ? user.name : '{{ __("N/A") }}'}<br>
                    <strong>{{ __('Product') }}:</strong> ${product ? product.name : '{{ __("N/A") }}'}<br>
                    <strong>{{ __('Comment') }}:</strong> ${feedback.comment.length > 100 ? feedback.comment.substring(0, 100) + '...' : feedback.comment}
                `;
                
                document.getElementById('delete-feedback-preview').innerHTML = previewHtml;
                adminPanel.openModal('deleteFeedbackModal');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("Error loading feedback details") }}');
            });
    };
    
    const feedbackDeleteBaseUrl = "{{ url('admin/feedbacks') }}";
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'confirmDeleteFeedback') {
            if (currentFeedbackId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = feedbackDeleteBaseUrl + '/' + currentFeedbackId;
                
                // CSRF
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken;
                form.appendChild(tokenInput);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    });
});
</script>
@endsection
