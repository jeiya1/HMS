$(document).ready(function () {
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
    $("#recovery-form").submit(function (e) {
        e.preventDefault(); // prevent normal form submission

        const email = $(this).find('input[name="email"]').val();

        showToast("Generating reset token, please wait...", "info");

        $.ajax({
            url: "/forgot-password", // your PHP handler
            type: "POST",
            data: { email: email },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    showToast(response.message || "Recovery email sent!", "success");
                } else {
                    showToast(response.error || "Failed to send recovery email.", "error");
                }
            },
            error: function () {
                showToast("Server error. Try again.", "error");
            }
        });
    });
    $("#reset-form").submit(function (e) {
        e.preventDefault();

        if (!validatePassword()) {
            showToast("Please fix password errors before submitting.", "error");
            return;
        }

        console.log($(this).serialize()); // <- check token is included

        $.ajax({
            url: "/reset-submit",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (!response.success) {
                    showToast(response.error, "error");
                } else {
                    showToast("Password Reset successful!", "success");
                    setTimeout(() => {
                        window.location.href = response.redirect || "/registration";
                    }, 1000);
                }
            },
            error: function () {
                showToast("Server error. Try again.", "error");
            }
        });
    });
    // ---------- UPDATE PROFILE ----------
    $("#update-form").submit(function (e) {
        e.preventDefault();

        let pass = $("#password").val();

        if (pass.length > 0 && !validatePassword()) {
            showToast("Fix password errors first.", "error");
            return;
        }

        $.ajax({
            url: "/update-submit",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                console.log(response);
                if (!response.success) {
                    showToast(response.error, "error");
                    console.log(response.error);
                } else {
                    showToast(response.message, "success");
                    console.log(response.message);
                }
            },
            error: function () {
                showToast("Server error. Try again.", "error");
            }
        });
    });
    $("#togglePassword").click(function () {
        const input = $("#password");
        const icon = $(this).find("img");

        if (input.attr("type") === "password") {
            input.attr("type", "text");
            icon.attr("src", "/assets/icons/eye-on.svg"); // eye ON
        } else {
            input.attr("type", "password");
            icon.attr("src", "/assets/icons/eye-off.svg"); // eye OFF
        }
    });
});