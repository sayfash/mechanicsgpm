// -------------------------------------------------------------
// MODULE: AUTHENTICATION & USER PROFILE MANAGEMENT
// -------------------------------------------------------------

async function fetchMe() {
    try {
        const data = await request('me', {}, 'GET');
        if (data.logged_in) {
            AppStore.user = data.user;
        } else {
            AppStore.user = null;
        }
    } catch (err) {
        AppStore.user = null;
    }
}
window.fetchMe = fetchMe;

async function fetchBranches() {
    try {
        const data = await request('get_branches', {}, 'GET');
        AppStore.branches = data || [];

        const mSelect = document.getElementById('mechanic-branch-select');
        const rSelect = document.getElementById('reg-branch');
        const fSelect = document.getElementById('super-admin-branch-filter');

        if (mSelect) {
            mSelect.innerHTML = AppStore.branches.map(b => `<option value="${b.id}">${escapeHtml(b.name)}</option>`).join('');
        }
        if (rSelect) {
            rSelect.innerHTML = '<option value="">No Branch Association (Super Admin Only)</option>' +
                AppStore.branches.map(b => `<option value="${b.id}">${escapeHtml(b.name)}</option>`).join('');
        }
        if (fSelect) {
            fSelect.innerHTML = '<option value="">All Branches</option>' +
                AppStore.branches.map(b => `<option value="${b.id}">${escapeHtml(b.name)}</option>`).join('');
        }
    } catch (err) {
        if (AppStore.user) {
            showToast(err.message, 'error');
        }
    }
}
window.fetchBranches = fetchBranches;

async function handleLogin(e) {
    if (e && e.preventDefault) e.preventDefault();
    const u = document.getElementById('login-username')?.value;
    const p = document.getElementById('login-password')?.value;

    try {
        const data = await request('login', { username: u, password: p });
        localStorage.setItem('auth_token', data.token || '');
        AppStore.user = data.user;
        showToast(data.message, 'success');

        if (document.getElementById('login-username')) document.getElementById('login-username').value = '';
        if (document.getElementById('login-password')) document.getElementById('login-password').value = '';

        if (typeof initializeApp === 'function') await initializeApp();
    } catch (err) {
        showToast(err.message, 'error');
    }
}
window.handleLogin = handleLogin;

function fillLogin(username, password, autoSubmit = false) {
    const uEl = document.getElementById('login-username');
    const pEl = document.getElementById('login-password');
    if (uEl) uEl.value = username;
    if (pEl) pEl.value = password;
    if (autoSubmit) {
        handleLogin();
    }
}
window.fillLogin = fillLogin;

function toggleAuthView(view) {
    const loginForm = document.getElementById('login-form-wrapper');
    const forgotForm = document.getElementById('forgot-form-wrapper');

    if (loginForm) loginForm.classList.add('hidden');
    if (forgotForm) forgotForm.classList.add('hidden');

    if (view === 'login' && loginForm) {
        loginForm.classList.remove('hidden');
    } else if (view === 'forgot' && forgotForm) {
        forgotForm.classList.remove('hidden');
    }
}
window.toggleAuthView = toggleAuthView;

async function handleForgotPasswordSubmit(e) {
    if (e && e.preventDefault) e.preventDefault();
    const u = document.getElementById('forgot-username')?.value?.trim();
    try {
        const data = await request('forgot_password', { username: u });
        showToast(data.message, 'warning', 8000);
        toggleAuthView('login');
    } catch (err) {
        showToast(err.message, 'error');
    }
}
window.handleForgotPasswordSubmit = handleForgotPasswordSubmit;

function openProfileModal() {
    const modal = document.getElementById('profile-modal');
    const usernameDisplay = document.getElementById('modal-profile-username-display');
    const displayNameInput = document.getElementById('profile-new-display-name');
    const oldPasswordInput = document.getElementById('profile-old-password');
    const newPasswordInput = document.getElementById('profile-new-password');
    const pictureInput = document.getElementById('profile-new-picture');
    const imgPreview = document.getElementById('modal-profile-picture-img');
    const iconPreview = document.getElementById('modal-profile-picture-icon');

    if (!modal) return;

    if (AppStore.user) {
        if (usernameDisplay) usernameDisplay.innerText = AppStore.user.username;
        if (displayNameInput) displayNameInput.value = AppStore.user.display_name || '';

        if (AppStore.user.profile_picture) {
            if (imgPreview) {
                imgPreview.src = AppStore.user.profile_picture + '?t=' + new Date().getTime();
                imgPreview.classList.remove('hidden');
            }
            if (iconPreview) iconPreview.classList.add('hidden');
        } else {
            if (iconPreview) iconPreview.classList.remove('hidden');
            if (imgPreview) {
                imgPreview.classList.add('hidden');
                imgPreview.src = '';
            }
        }
    }

    if (oldPasswordInput) oldPasswordInput.value = '';
    if (newPasswordInput) newPasswordInput.value = '';
    if (pictureInput) pictureInput.value = '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const menu = document.getElementById('profile-dropdown');
    if (menu) menu.classList.add('hidden');
}
window.openProfileModal = openProfileModal;

function previewSelectedProfilePicture(e) {
    const file = e.target.files[0];
    if (!file) return;

    if (file.size > 1048576) {
        showToast('Image size exceeds 1MB limit.', 'error');
        e.target.value = '';
        return;
    }

    const imgPreview = document.getElementById('modal-profile-picture-img');
    const iconPreview = document.getElementById('modal-profile-picture-icon');
    if (imgPreview && iconPreview) {
        imgPreview.src = URL.createObjectURL(file);
        imgPreview.classList.remove('hidden');
        iconPreview.classList.add('hidden');
    }
}
window.previewSelectedProfilePicture = previewSelectedProfilePicture;

function closeProfileModal() {
    const modal = document.getElementById('profile-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
window.closeProfileModal = closeProfileModal;

async function handleProfileUpdate(e) {
    if (e && e.preventDefault) e.preventDefault();
    const newDisplayName = document.getElementById('profile-new-display-name')?.value?.trim();
    const oldPassword = document.getElementById('profile-old-password')?.value;
    const newPassword = document.getElementById('profile-new-password')?.value;
    const pictureFile = document.getElementById('profile-new-picture')?.files[0];

    try {
        const formData = new FormData();
        formData.append('action', 'update_profile');
        if (newDisplayName) formData.append('display_name', newDisplayName);
        if (oldPassword) formData.append('old_password', oldPassword);
        if (newPassword) formData.append('new_password', newPassword);
        if (pictureFile) formData.append('profile_picture', pictureFile);

        const res = await requestFormData(formData);
        showToast(res.message, 'success');

        // Update frontend state username and picture
        AppStore.user.display_name = res.display_name;
        // Ensure profile_picture can be null without breaking UI
        AppStore.user.profile_picture = res.profile_picture || null;
        const nameEl = document.getElementById('user-display-name');
        if (nameEl) nameEl.innerText = res.display_name;
        updateProfilePictureUI();

        closeProfileModal();
    } catch (err) {
        showToast(err.message, 'error');
    }
}
window.handleProfileUpdate = handleProfileUpdate;

function updateProfilePictureUI() {
    const icon = document.getElementById('profile-picture-icon');
    const img = document.getElementById('profile-picture-img');
    const mobileImg = document.getElementById('mobile-profile-img');
    const mobileIcon = document.getElementById('mobile-profile-icon');
    const mobileDropdownImg = document.getElementById('mobile-dropdown-profile-img');
    const mobileDropdownIcon = document.getElementById('mobile-dropdown-profile-icon');
    const dropdownImg = document.getElementById('dropdown-profile-img');
    const dropdownIcon = document.getElementById('dropdown-profile-icon');

    if (AppStore.user && AppStore.user.profile_picture) {
        const src = AppStore.user.profile_picture + '?t=' + new Date().getTime();
        if (icon) icon.classList.add('hidden');
        if (img) {
            img.src = src;
            img.classList.remove('hidden');
        }
        if (mobileIcon) mobileIcon.classList.add('hidden');
        if (mobileImg) {
            mobileImg.src = src;
            mobileImg.classList.remove('hidden');
        }
        if (mobileDropdownIcon) mobileDropdownIcon.classList.add('hidden');
        if (mobileDropdownImg) {
            mobileDropdownImg.src = src;
            mobileDropdownImg.classList.remove('hidden');
        }
        if (dropdownIcon) dropdownIcon.classList.add('hidden');
        if (dropdownImg) {
            dropdownImg.src = src;
            dropdownImg.classList.remove('hidden');
        }
    } else {
        if (icon) icon.classList.remove('hidden');
        if (img) {
            img.classList.add('hidden');
            img.src = '';
        }
        if (mobileIcon) mobileIcon.classList.remove('hidden');
        if (mobileImg) {
            mobileImg.classList.add('hidden');
            mobileImg.src = '';
        }
        if (mobileDropdownIcon) mobileDropdownIcon.classList.remove('hidden');
        if (mobileDropdownImg) {
            mobileDropdownImg.classList.add('hidden');
            mobileDropdownImg.src = '';
        }
        if (dropdownIcon) dropdownIcon.classList.remove('hidden');
        if (dropdownImg) {
            dropdownImg.classList.add('hidden');
            dropdownImg.src = '';
        }
    }
}
window.updateProfilePictureUI = updateProfilePictureUI;

async function logout() {
    try {
        const data = await request('logout');
        localStorage.removeItem('auth_token');
        localStorage.removeItem('sgpm_last_route');
        showToast(data.message, 'success');
        AppStore.user = null;

        if (AppStore.activeTimersInterval) {
            clearInterval(AppStore.activeTimersInterval);
        }

        document.getElementById('nav-header')?.classList.add('hidden');
        if (typeof showSection === 'function') showSection('auth-gate');
    } catch (err) {
        showToast(err.message, 'error');
    }
}
window.logout = logout;
