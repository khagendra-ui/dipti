(function($) {
    'use strict';

    /**
     * CareOnCall Frontend JavaScript
     * Handles user interactions and AJAX calls
     */

    var careoncallApp = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Search caretakers
            $(document).on('keyup', '#careoncall-search-input', careoncallApp.searchCaretakers);

            // Book caretaker
            $(document).on('click', '.careoncall-book-btn', careoncallApp.showBookingForm);

            // Submit booking
            $(document).on('click', '#careoncall-submit-booking', careoncallApp.submitBooking);

            // Cancel booking form
            $(document).on('click', '.careoncall-cancel-booking', careoncallApp.cancelBooking);

            // Respond to booking request (caretaker)
            $(document).on('click', '.careoncall-respond-booking', careoncallApp.respondToBooking);

            // Update profile
            $(document).on('click', '#careoncall-update-profile', careoncallApp.updateProfile);
        },

        searchCaretakers: function() {
            var query = $(this).val();

            if (query.length < 2) {
                $('#careoncall-results').html('');
                return;
            }

            $.ajax({
                type: 'GET',
                url: careoncallVars.admin_ajax,
                data: {
                    action: 'careoncall_search_caretakers',
                    query: query
                },
                success: function(response) {
                    if (response.success) {
                        var html = '';
                        $.each(response.data, function(index, caretaker) {
                            html += '<div class="careoncall-caretaker-card">';
                            html += '<h3>' + caretaker.name + '</h3>';
                            html += '<p><strong>Rate:</strong> $' + parseFloat(caretaker.hourly_rate).toFixed(2) + '/hour</p>';
                            html += '<p><strong>Experience:</strong> ' + caretaker.years_experience + ' years</p>';
                            html += '<p><strong>Skills:</strong> ' + caretaker.skills + '</p>';
                            html += '<button class="careoncall-book-btn button button-primary" data-caretaker-id="' + caretaker.ID + '" data-caretaker-name="' + caretaker.name + '" data-caretaker-rate="' + caretaker.hourly_rate + '">Book Now</button>';
                            html += '</div>';
                        });
                        $('#careoncall-results').html(html);
                    } else {
                        $('#careoncall-results').html('<p>No caretakers found.</p>');
                    }
                }
            });
        },

        showBookingForm: function(e) {
            e.preventDefault();

            var caretakerId = $(this).data('caretaker-id');
            var caretakerName = $(this).data('caretaker-name');
            var hourlyRate = $(this).data('caretaker-rate');

            var html = '<div class="careoncall-booking-form-modal">';
            html += '<div class="modal-content">';
            html += '<span class="close careoncall-cancel-booking">&times;</span>';
            html += '<h2>Book ' + caretakerName + '</h2>';
            html += '<form id="careoncall-booking-form">';
            html += '<div class="form-group">';
            html += '<label for="booking-date">Date:</label>';
            html += '<input type="date" id="booking-date" name="booking_date" required>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<label for="booking-start-time">Start Time:</label>';
            html += '<input type="time" id="booking-start-time" name="start_time" required>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<label for="booking-end-time">End Time:</label>';
            html += '<input type="time" id="booking-end-time" name="end_time" required onchange="careoncallApp.calculateCost()">';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<label for="booking-location">Location:</label>';
            html += '<input type="text" id="booking-location" name="location" required>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<label for="booking-notes">Special Requests (Optional):</label>';
            html += '<textarea id="booking-notes" name="notes" rows="4"></textarea>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<p><strong>Estimated Cost: $<span id="estimated-cost">0.00</span></strong></p>';
            html += '</div>';
            html += '<input type="hidden" name="caretaker_id" value="' + caretakerId + '">';
            html += '<input type="hidden" name="hourly_rate" value="' + hourlyRate + '">';
            html += '<button type="button" id="careoncall-submit-booking" class="button button-primary" style="width: 100%; margin-top: 10px;">Confirm Booking</button>';
            html += '</form>';
            html += '</div>';
            html += '</div>';

            $('body').append(html);
            $('.careoncall-booking-form-modal').fadeIn();

            // Set minimum date to today
            var today = new Date().toISOString().split('T')[0];
            $('#booking-date').attr('min', today);
        },

        calculateCost: function() {
            var startTime = $('#booking-start-time').val();
            var endTime = $('#booking-end-time').val();
            var hourlyRate = parseFloat($('input[name="hourly_rate"]').val());

            if (startTime && endTime) {
                var start = new Date('2000/01/01 ' + startTime);
                var end = new Date('2000/01/01 ' + endTime);
                var hours = (end - start) / (1000 * 60 * 60);

                if (hours > 0) {
                    var cost = (hours * hourlyRate).toFixed(2);
                    $('#estimated-cost').text(cost);
                }
            }
        },

        submitBooking: function(e) {
            e.preventDefault();

            var formData = {
                caretaker_id: $('input[name="caretaker_id"]').val(),
                booking_date: $('#booking-date').val(),
                start_time: $('#booking-start-time').val(),
                end_time: $('#booking-end-time').val(),
                location: $('#booking-location').val(),
                notes: $('#booking-notes').val()
            };

            $.ajax({
                type: 'POST',
                url: careoncallVars.admin_ajax,
                data: {
                    action: 'careoncall_create_booking',
                    data: formData,
                    nonce: careoncallVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Booking submitted successfully! Waiting for caretaker response.');
                        $('.careoncall-booking-form-modal').fadeOut(300, function() {
                            $(this).remove();
                        });
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

        cancelBooking: function(e) {
            e.preventDefault();
            $('.careoncall-booking-form-modal').fadeOut(300, function() {
                $(this).remove();
            });
        },

        respondToBooking: function(e) {
            e.preventDefault();

            var bookingId = $(this).data('booking-id');
            var response = $(this).data('response');
            var self = $(this);

            if (response === 'accepted') {
                if (!confirm('Do you want to accept this booking?')) {
                    return;
                }
            } else {
                if (!confirm('Do you want to decline this booking?')) {
                    return;
                }
            }

            $.ajax({
                type: 'POST',
                url: careoncallVars.admin_ajax,
                data: {
                    action: 'careoncall_respond_booking',
                    booking_id: bookingId,
                    response: response,
                    nonce: careoncallVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Your response has been recorded.');
                        self.closest('.booking-request').fadeOut(300, function() {
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

        updateProfile: function(e) {
            e.preventDefault();

            var formData = {
                first_name: $('#first-name').val(),
                last_name: $('#last-name').val(),
                email: $('#email').val()
            };

            // Caretaker-specific fields
            if ($('#hourly-rate').length) {
                formData.hourly_rate = $('#hourly-rate').val();
                formData.years_experience = $('#years-experience').val();
                formData.skills = $('#skills').val();
            }

            $.ajax({
                type: 'POST',
                url: careoncallVars.admin_ajax,
                data: {
                    action: 'careoncall_update_profile',
                    data: formData,
                    nonce: careoncallVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Profile updated successfully!');
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    };

    // Modal styles
    var styles = `
        <style>
            .careoncall-booking-form-modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.4);
            }

            .careoncall-booking-form-modal .modal-content {
                background-color: #fefefe;
                margin: 5% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 5px;
                width: 90%;
                max-width: 500px;
                max-height: 80vh;
                overflow-y: auto;
            }

            .careoncall-booking-form-modal .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }

            .careoncall-booking-form-modal .close:hover {
                color: black;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: 500;
            }

            .form-group input,
            .form-group textarea {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-family: inherit;
                box-sizing: border-box;
            }

            .form-group input:focus,
            .form-group textarea:focus {
                outline: none;
                border-color: #4CAF50;
                box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
            }

            .careoncall-caretaker-card {
                background: white;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 15px;
                margin-bottom: 15px;
            }

            .careoncall-caretaker-card h3 {
                margin: 0 0 10px 0;
                color: #333;
            }

            .careoncall-caretaker-card p {
                margin: 5px 0;
                color: #666;
            }

            .careoncall-caretaker-card .button {
                margin-top: 10px;
            }
        </style>
    `;
    
    $(function() {
        $('head').append(styles);
        careoncallApp.init();
    });

})(jQuery);
