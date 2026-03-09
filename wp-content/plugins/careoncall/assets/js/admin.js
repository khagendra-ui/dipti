(function($) {
    'use strict';

    /**
     * CareOnCall Admin JavaScript
     * Handles admin interface interactions
     */

    var careoncallAdmin = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Verify caretaker button
            $(document).on('click', '.careoncall-verify-btn', careoncallAdmin.verifyCaretaker);

            // Reject caretaker button
            $(document).on('click', '.careoncall-reject-btn', careoncallAdmin.rejectCaretaker);

            // Approve booking button
            $(document).on('click', '.careoncall-approve-booking', careoncallAdmin.approveBooking);

            // Reject booking button
            $(document).on('click', '.careoncall-reject-booking', careoncallAdmin.rejectBooking);

            // Log pagination
            $(document).on('click', '.careoncall-log-pagination a', careoncallAdmin.loadLogs);
        },

        verifyCaretaker: function(e) {
            e.preventDefault();

            var caretakerId = $(this).data('caretaker-id');
            var self = $(this);

            if (!confirm('Are you sure you want to verify this caretaker?')) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: careoncallAdminVars.admin_ajax,
                data: {
                    action: 'careoncall_verify_caretaker',
                    caretaker_id: caretakerId,
                    nonce: careoncallAdminVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Caretaker verified successfully!');
                        self.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        },

        rejectCaretaker: function(e) {
            e.preventDefault();

            var caretakerId = $(this).data('caretaker-id');
            var self = $(this);
            var reason = prompt('Enter rejection reason:');

            if (reason === null) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: careoncallAdminVars.admin_ajax,
                data: {
                    action: 'careoncall_reject_caretaker',
                    caretaker_id: caretakerId,
                    reason: reason,
                    nonce: careoncallAdminVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Caretaker rejected successfully!');
                        self.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        },

        approveBooking: function(e) {
            e.preventDefault();

            var bookingId = $(this).data('booking-id');
            var self = $(this);

            if (!confirm('Are you sure you want to approve this booking?')) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: careoncallAdminVars.admin_ajax,
                data: {
                    action: 'careoncall_approve_booking',
                    booking_id: bookingId,
                    nonce: careoncallAdminVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Booking approved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        },

        rejectBooking: function(e) {
            e.preventDefault();

            var bookingId = $(this).data('booking-id');
            var self = $(this);
            var reason = prompt('Enter rejection reason:');

            if (reason === null) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: careoncallAdminVars.admin_ajax,
                data: {
                    action: 'careoncall_reject_booking',
                    booking_id: bookingId,
                    reason: reason,
                    nonce: careoncallAdminVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Booking rejected successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        },

        loadLogs: function(e) {
            e.preventDefault();

            var page = $(this).data('page');

            $.ajax({
                type: 'POST',
                url: careoncallAdminVars.admin_ajax,
                data: {
                    action: 'careoncall_load_logs',
                    page: page,
                    nonce: careoncallAdminVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.careoncall-logs-table').html(response.data.table);
                        $('.careoncall-log-pagination').html(response.data.pagination);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    };

    // Ready
    $(document).ready(function() {
        careoncallAdmin.init();
    });

})(jQuery);
