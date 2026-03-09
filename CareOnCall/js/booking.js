// Booking calculation
document.getElementById('start_time')?.addEventListener('change', calculateCost);
document.getElementById('end_time')?.addEventListener('change', calculateCost);
document.getElementById('caretaker_id')?.addEventListener('change', calculateCost);

function calculateCost() {
    const caretakerId = document.getElementById('caretaker_id');
    const startTimeEl = document.getElementById('start_time');
    const endTimeEl = document.getElementById('end_time');
    const estimatedCostEl = document.getElementById('estimated_cost');

    if (!caretakerId.value || !startTimeEl.value || !endTimeEl.value) {
        return;
    }

    const selectedOption = caretakerId.options[caretakerId.selectedIndex];
    const hourlyRate = parseFloat(selectedOption.dataset.rate);

    const [startHour, startMin] = startTimeEl.value.split(':').map(Number);
    const [endHour, endMin] = endTimeEl.value.split(':').map(Number);

    let startMinutes = startHour * 60 + startMin;
    let endMinutes = endHour * 60 + endMin;

    if (endMinutes <= startMinutes) {
        endMinutes += 24 * 60; // Next day
    }

    const hours = (endMinutes - startMinutes) / 60;
    const cost = hours * hourlyRate;

    estimatedCostEl.textContent = '$' + cost.toFixed(2);
}

function updateCaretaker() {
    calculateCost();
}

// Form validation
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!validateForm(this)) {
            e.preventDefault();
        }
    });
});

function validateForm(form) {
    let isValid = true;

    form.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'red';
            isValid = false;
        } else {
            field.style.borderColor = '';
        }
    });

    return isValid;
}

// Smooth scroll for navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#' && document.querySelector(href)) {
            e.preventDefault();
            document.querySelector(href).scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Modal functionality
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});

// Confirmation dialogs
function confirmAction(message) {
    return confirm(message);
}

// Date validation
function validateDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return date >= today;
}
