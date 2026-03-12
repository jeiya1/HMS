document.addEventListener("DOMContentLoaded", () => {

    const password = document.querySelector("#password");
    const passwordr = document.querySelector("#passwordr");
    const errorBox = document.querySelector("#password-error");

    let timer;

    password.addEventListener("input", () => {

        clearTimeout(timer);

        timer = setTimeout(() => {

            let errors = [];
            let pass = password.value;

            if (pass.length < 8) {
                errors.push("Minimum 8 characters");
            }

            if (!/[A-Z]/.test(pass)) {
                errors.push("Must contain uppercase letter");
            }

            if (!/[a-z]/.test(pass)) {
                errors.push("Must contain lowercase letter");
            }

            if (!/[0-9]/.test(pass)) {
                errors.push("Must contain number");
            }

            if (!/[\W]/.test(pass)) {
                errors.push("Must contain special character");
            }

            if (password != passwordr) {
                errors.push("Passwords do not match");
            }

            if (errors.length > 0) {
                errorBox.innerHTML = errors.join("<br>");
            } else {
                errorBox.innerHTML = "Strong password";
            }

        }, 500);

    });

});