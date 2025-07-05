const API_URL = 'http://localhost:8000/api';
function showToast(message, type = 'success') {
  const toast = document.createElement('div');
  toast.textContent = message;
  toast.className = `toast ${type}`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}


document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const quoteForm = document.getElementById('quoteForm');
    const logoutBtn = document.getElementById('logoutBtn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            localStorage.removeItem('token');
            showToast('Logged out', 'success');
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 1000);
        });
    }

  if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        const res = await fetch(`${API_URL}/auth/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, password }),
        });

        const data = await res.json();
        showToast(res.ok ? 'Registered successfully!' : 'Error', res.ok ? 'success' : 'error')
        res.ok && (window.location.href = 'index.html');

        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        const res = await fetch(`${API_URL}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password }),
        });

        const data = await res.json();

        if (res.ok && data.access_token) {
            localStorage.setItem('token', data.access_token);
            window.location.href = 'quotation.html';
        } else {
            showToast(data.error || 'Login failed', 'error')
            }
        });
    }

    if (quoteForm) {
        quoteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const token = localStorage.getItem('token');
            if (!token) return showToast('Not authenticated', 'error');

            const body = {
                age: document.getElementById('age').value,
                currency_id: document.getElementById('currency').value,
                start_date: document.getElementById('start').value,
                end_date: document.getElementById('end').value,
            };

            try {
                const res = await fetch(`${API_URL}/quotation`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                    },
                    body: JSON.stringify(body),
                });

                const data = await res.json();

                if (res.status === 401) {
                    localStorage.removeItem('token');
                    showToast('Session expired. Please log in again.', 'error');
                    setTimeout(() => window.location.href = 'index.html', 1500);
                    return;
                }

                if (!res.ok) {
                    showToast(data.message || 'Error generating quotation', 'error');
                    return;
                }

                showToast(`Quotation ID: ${data.data.quotation_id} | Total: ${data.data.total} ${data.data.currency_id}`);
                const tableBody = document.getElementById('quoteTableBody');
                const quoteTable = document.getElementById('quoteTable');

                tableBody.innerHTML = `
                <tr>
                    <td>${data.data.quotation_id}</td>
                    <td>${data.data.total}</td>
                    <td>${data.data.currency_id}</td>
                </tr>
                `;
                quoteTable.style.display = 'table';

            } catch (err) {
                showToast('Something went wrong', 'error');
            }
        });
    }
});
