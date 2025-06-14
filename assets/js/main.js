// Main JavaScript file

// Document ready function
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Initialize popovers
    $('[data-toggle="popover"]').popover();

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Form validation
    $('form').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });

    // File input preview
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Confirm delete
    $('.delete-confirm').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });

    // Dynamic form fields
    $('.add-field').on('click', function() {
        let template = $(this).data('template');
        let container = $(this).data('container');
        $(container).append(template);
    });

    // Remove dynamic form fields
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.dynamic-field').remove();
    });

    // Shipment status update
    $('.status-update').on('change', function() {
        let shipmentId = $(this).data('shipment-id');
        let newStatus = $(this).val();
        
        $.ajax({
            url: 'api/v1/shipments/' + shipmentId + '/status',
            method: 'PUT',
            data: { status: newStatus },
            success: function(response) {
                showAlert('success', 'Status updated successfully');
            },
            error: function() {
                showAlert('danger', 'Failed to update status');
            }
        });
    });

    // Document upload progress
    $('.document-upload').on('change', function() {
        let file = this.files[0];
        let formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: 'api/v1/documents/upload',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                let xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        let percent = Math.round((e.loaded / e.total) * 100);
                        $('.upload-progress').css('width', percent + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                showAlert('success', 'File uploaded successfully');
            },
            error: function() {
                showAlert('danger', 'Failed to upload file');
            }
        });
    });
});

// Show alert message
function showAlert(type, message) {
    let alert = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    $('.alert-container').html(alert);
}

// Format date
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Validate email
function validateEmail(email) {
    let re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validate phone number
function validatePhone(phone) {
    let re = /^\+?[\d\s-]{10,}$/;
    return re.test(phone);
}

// Generate random ID
function generateId() {
    return Math.random().toString(36).substr(2, 9);
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function executedFunction(...args) {
        if (!inThrottle) {
            func(...args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
} 