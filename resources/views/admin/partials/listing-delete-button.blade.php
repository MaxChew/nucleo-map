@php
    $name = $name ?? 'item';
    $confirmTitle = $confirmTitle ?? 'Are you sure?';
    $confirmText = $confirmText ?? "You won't be able to revert this!";
    $confirmButtonText = $confirmButtonText ?? 'Yes, delete it!';
    $cancelButtonText = $cancelButtonText ?? 'Cancel';
    $successTitle = $successTitle ?? 'Deleted!';
    $successText = $successText ?? ucfirst($name) . ' has been deleted.';
    $errorTitle = $errorTitle ?? 'Error!';
    $errorText = $errorText ?? 'There was an error deleting the ' . $name . '.';
@endphp

<button
    @click="deleteItem(row.id, '{{ $name }}')"
    class="text-danger-600 hover:text-danger-900 transition-colors"
    aria-label="Delete {{ $name }}"
    data-balloon-pos="up-right"
    v-if="row.deleted_at == null"
>
    <i class="fad fa-trash text-base pl-2"></i>
</button>

@push('scripts')
<script>
    // Global delete function
window.deleteItem = function(itemId, itemName) {
    const confirmTitle = '{{ $confirmTitle }}';
    const confirmText = '{{ $confirmText }}';
    const confirmButtonText = '{{ $confirmButtonText }}';
    const cancelButtonText = '{{ $cancelButtonText }}';
    const successTitle = '{{ $successTitle }}';
    const successText = '{{ $successText }}';
    const errorTitle = '{{ $errorTitle }}';
    const errorText = '{{ $errorText }}';

    Swal.fire({
        title: confirmTitle,
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText
    }).then((result) => {
        if (result.isConfirmed) {
            // Build delete URL
            const currentUrl = window.location.pathname;
            const baseUrl = currentUrl.replace(/\/[^\/]*$/, '');
            const deleteUrl = `${baseUrl}/${itemId}`;

            // Send delete request
            axios.delete(deleteUrl)
                .then(response => {
                    Swal.fire({
                        title: successTitle,
                        text: successText,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Refresh list
                    if (window.app && window.app.$refs.listing) {
                        window.app.$refs.listing.refresh();
                    } else {
                        // If no Vue instance, reload the page
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: errorTitle,
                        text: errorText,
                        icon: 'error'
                    });
                    console.error('Delete error:', error);
                });
        }
    });
};
</script>
@endpush 