// API Configuration
const API_URL = 'http://localhost/CRUD_API/crud.php';

// DOM Elements
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

// Forms
const forms = {
    register: document.getElementById('registerForm'),
    retrieve: document.getElementById('retrieveForm'),
    update: document.getElementById('updateForm'),
    delete: document.getElementById('deleteForm')
};

// Message Elements
const messages = {
    register: document.getElementById('registerMessage'),
    retrieve: document.getElementById('retrieveMessage'),
    update: document.getElementById('updateMessage'),
    delete: document.getElementById('deleteMessage')
};

// User Details Display
const userDetails = document.getElementById('userDetails');

// Tab Switching
tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const tabName = btn.getAttribute('data-tab');
        
        // Update active states
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        btn.classList.add('active');
        document.getElementById(tabName).classList.add('active');

        // Clear previous state
        Object.values(messages).forEach(hideMessage);
        if (tabName !== 'retrieve') {
            userDetails.classList.remove('show');
        }
    });
});

// Helper Functions
function showMessage(element, message, type) {
    const icon = type === 'success' ? '✅' : (type === 'error' ? '❌' : 'ℹ️');
    element.innerHTML = `<span>${icon}</span> ${message}`;
    element.className = `message show ${type}`;
    
    // Auto-hide success messages after 5s, keep errors longer
    if (type !== 'error') {
        setTimeout(() => {
            element.classList.remove('show');
        }, 5000);
    }
}

function hideMessage(element) {
    element.classList.remove('show');
}

function setLoader(form, isLoading) {
    const btn = form.querySelector('button[type="submit"]');
    if (isLoading) {
        btn.classList.add('loading');
        btn.disabled = true;
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = '<span>⏳</span> Processing...';
    } else {
        btn.classList.remove('loading');
        btn.disabled = false;
        btn.innerHTML = btn.dataset.originalText || btn.innerHTML;
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
    return /^[0-9]{10,}$/.test(phone.replace(/[\s\-\(\)]/g, ''));
}

// 1. REGISTER USER
forms.register.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideMessage(messages.register);
    
    const formData = new FormData(forms.register);
    const data = Object.fromEntries(formData.entries());
    
    if (data.password.length < 6) {
        showMessage(messages.register, 'Password must be at least 6 characters', 'error');
        return;
    }

    try {
        setLoader(forms.register, true);
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ...data, operation: '1' })
        });

        const result = await response.json();
        if (result.message) {
            showMessage(messages.register, result.message, 'success');
            forms.register.reset();
        } else {
            showMessage(messages.register, result.error || 'Registration failed', 'error');
        }
    } catch (error) {
        showMessage(messages.register, 'Network error. Please try again.', 'error');
    } finally {
        setLoader(forms.register, false);
    }
});

// 2. RETRIEVE USER
forms.retrieve.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideMessage(messages.retrieve);
    userDetails.classList.remove('show');

    const id = document.getElementById('retrieve-id').value;
    
    try {
        setLoader(forms.retrieve, true);
        const response = await fetch(`${API_URL}?operation=2&id=${id}`);
        const data = await response.json();
        
        if (response.ok && !data.error) {
            displayUserDetails(data, id);
        } else {
            showMessage(messages.retrieve, data.error || 'User not found', 'error');
        }
    } catch (error) {
        showMessage(messages.retrieve, 'Network error', 'error');
    } finally {
        setLoader(forms.retrieve, false);
    }
});

function displayUserDetails(user, id) {
    userDetails.innerHTML = `
        <h3>👤 User Profile</h3>
        <div class="user-detail-item">
            <span class="user-detail-label">ID</span>
            <span class="user-detail-value">#${id}</span>
        </div>
        <div class="user-detail-item">
            <span class="user-detail-label">Name</span>
            <span class="user-detail-value">${escapeHtml(user.name)}</span>
        </div>
        <div class="user-detail-item">
            <span class="user-detail-label">Email</span>
            <span class="user-detail-value">${escapeHtml(user.email)}</span>
        </div>
        <div class="user-detail-item">
            <span class="user-detail-label">Phone</span>
            <span class="user-detail-value">${escapeHtml(user.phone)}</span>
        </div>
        <div class="user-detail-item">
            <span class="user-detail-label">Role</span>
            <span class="user-detail-value" style="text-transform: capitalize;">${escapeHtml(user.role)}</span>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 8px;">
            <button onclick="prefillUpdate(${id}, '${escapeHtml(user.name)}', '${escapeHtml(user.email)}', '${escapeHtml(user.phone)}', '${escapeHtml(user.role)}')" class="btn btn-primary" style="font-size: 0.75rem; padding: 8px 12px; flex: 1;">Edit User</button>
        </div>
    `;
    userDetails.classList.add('show');
}

// Prefill Update Form Helper
window.prefillUpdate = (id, name, email, phone, role) => {
    document.getElementById('update-id').value = id;
    document.getElementById('update-name').value = name;
    document.getElementById('update-email').value = email;
    document.getElementById('update-phone').value = phone;
    document.getElementById('update-role').value = role;
    
    // Switch to Update Tab
    const updateTabBtn = document.querySelector('[data-tab="update"]');
    updateTabBtn.click();
};

// 3. UPDATE USER
forms.update.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideMessage(messages.update);
    
    const formData = new FormData(forms.update);
    const params = new URLSearchParams(formData);
    params.append('operation', '3');
    
    try {
        setLoader(forms.update, true);
        const response = await fetch(API_URL, {
            method: 'PUT',
            body: params.toString()
        });
        
        const data = await response.json();
        if (data.message) {
            showMessage(messages.update, data.message, 'success');
            // If the user being updated is currently viewed in Search, refresh it?
            // For now just success.
        } else {
            showMessage(messages.update, data.error || 'Update failed', 'error');
        }
    } catch (error) {
        showMessage(messages.update, 'Network error', 'error');
    } finally {
        setLoader(forms.update, false);
    }
});

// 4. DELETE USER
forms.delete.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideMessage(messages.delete);
    
    const id = document.getElementById('delete-id').value;
    
    if (!confirm('⚠️ This will permanently remove the user. Proceed?')) return;
    
    try {
        setLoader(forms.delete, true);
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ operation: '4', id: id })
        });
        
        const data = await response.json();
        if (data.message) {
            showMessage(messages.delete, data.message, 'success');
            forms.delete.reset();
        } else {
            showMessage(messages.delete, data.error || 'Deletion failed', 'error');
        }
    } catch (error) {
        showMessage(messages.delete, 'Network error', 'error');
    } finally {
        setLoader(forms.delete, false);
    }
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
