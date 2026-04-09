$(document).ready(function () {
    const signupPassword = $("#signup-form .password-field"); // only the signup password
    const signupErrorBox = $("#password-error");
    let timer;

    function validateSignupPassword() {
        let errors = [];
        let pass = signupPassword.val();

        if (pass.length < 8) errors.push("Minimum 8 characters");
        if (!/[A-Z]/.test(pass)) errors.push("Must contain uppercase letter");
        if (!/[a-z]/.test(pass)) errors.push("Must contain lowercase letter");
        if (!/[0-9]/.test(pass)) errors.push("Must contain number");
        if (!/[\W]/.test(pass)) errors.push("Must contain special character");

        if (errors.length > 0) {
            signupErrorBox.html(errors.join("<br>"));
            return false;
        } else {
            signupErrorBox.html("");
            return true;
        }
    }

    function validatePassword() {
        let errors = [];
        let pass = $("#password").val(); // the new password field

        if (pass.length < 8) errors.push("Minimum 8 characters");
        if (!/[A-Z]/.test(pass)) errors.push("Must contain uppercase letter");
        if (!/[a-z]/.test(pass)) errors.push("Must contain lowercase letter");
        if (!/[0-9]/.test(pass)) errors.push("Must contain number");
        if (!/[\W]/.test(pass)) errors.push("Must contain special character");

        if (errors.length > 0) {
            $("#password-error").html(errors.join("<br>"));
            return false;
        } else {
            $("#password-error").html("");
            return true;
        }
    }

    // Input listener
    signupPassword.on("input", function () {
        clearTimeout(timer);
        timer = setTimeout(validateSignupPassword, 500);
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
                    window.location.href = response.redirect || "/home";
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

        if (!validateSignupPassword()) {
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
                    window.location.href = response.redirect || "/home";
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
                    window.location.href = response.redirect || "/home";
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
                } else {
                    showToast(response.message, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                }
            },
            error: function () {
                showToast("Server error. Try again.", "error");
            }
        });
    });
    $(".toggle-password").click(function () {
        const input = $(this).siblings(".password-field");
        const icon = $(this).find("img");

        if (input.attr("type") === "password") {
            input.attr("type", "text");
            icon.attr("src", "/assets/icons/eye-on.svg");
        } else {
            input.attr("type", "password");
            icon.attr("src", "/assets/icons/eye-off.svg");
        }
    });
});