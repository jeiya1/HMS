$(document).ready(function () {

    // ---------- Helper ----------
    function showToast(message, type) {
        const event = new CustomEvent("showToast", { detail: { message, type } });
        document.dispatchEvent(event);
    }

    // ---------- PASSWORD VALIDATION ----------
    const password = $("#password");
    const errorBox = $("#password-error");
    let timer;

    function validatePassword() {
        let errors = [];
        let pass = password.val();

        if (pass.length < 8) errors.push("Minimum 8 characters");
        if (!/[A-Z]/.test(pass)) errors.push("Must contain uppercase letter");
        if (!/[a-z]/.test(pass)) errors.push("Must contain lowercase letter");
        if (!/[0-9]/.test(pass)) errors.push("Must contain number");
        if (!/[\W]/.test(pass)) errors.push("Must contain special character");

        if (errors.length > 0) {
            errorBox.html(errors.join("<br>"));
            return false;
        } else {
            errorBox.html("");
            return true;
        }
    }

    password.on("input", function () {
        clearTimeout(timer);
        timer = setTimeout(validatePassword, 500);
    });

    // ---------- LOGIN ----------
    $("#loginForm").submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: "/login-submit",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (!response.success) {
                    showToast(response.error, "error");
                } else {
                    showToast("Login successful!", "success");
                    setTimeout(() => window.location.href = response.redirect, 1000);
                }
            },
            error: function () {
                showToast("Server error. Try again.", "error");
            }
        });
    });

    // ---------- SIGNUP ----------
    $("#signup-form").submit(function (e) {
        e.preventDefault();

        if (!validatePassword()) {
            showToast("Please fix password errors before submitting.", "error");
            return;
        }

        $.ajax({
            url: "/signup-submit",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (!response.success) {
                    showToast(response.error, "error");
                } else {
                    showToast("Signup successful!", "success");
                    setTimeout(() => {
                        window.location.href = response.redirect || "/home";
                    }, 1000);
                }
            },
            error: function () {
                showToast("Server error. Try again.", "error");
            }
        });
    });

    // ---------- LOGOUT ----------
    $("#logoutBtn").click(function (e) {
        e.preventDefault();
        $.ajax({
            url: "/logout",
            type: "POST",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    showToast("Logged out successfully!", "success");
                    setTimeout(() => window.location.href = response.redirect || "/home", 500);
                } else {
                    showToast("Logout failed. Try again.", "error");
                }
            },
            error: function () {
                showToast("Server error. Try again.", "error");
            }
        });
    });

});