console.log("Login JS loaded");
$("#loginForm").submit(function(e) {
    e.preventDefault();

    $.ajax({
        url: "/login-submit",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function(response) {

            if (!response.success) {
                showToast(response.error, "error");
            } else {
                showToast("Login successful!", "success");

                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1000);
            }

        },
        error: function() {
            showToast("Server error. Try again.", "error");
        }
    });
});