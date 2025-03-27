document.addEventListener("DOMContentLoaded", function () {
    const signUpButton = document.getElementById("signUpButton");
    const signInButton = document.getElementById("signInButton");
    const signInForm = document.getElementById("signIn");
    const signUpForm = document.getElementById("signup");

    // Form switching
    signUpButton?.addEventListener("click", function () {
        signInForm.classList.add("hidden");
        signUpForm.classList.remove("hidden");
    });

    signInButton?.addEventListener("click", function () {
        signInForm.classList.remove("hidden");
        signUpForm.classList.add("hidden");
    });

    // Register form submission
    const registerForm = document.getElementById("registerForm");
    registerForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("register.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful!',
                    text: 'Your account has been created.',
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: {
                        popup: 'bg-white rounded-lg shadow-xl',
                        title: 'text-xl font-bold text-gray-900',
                        text: 'text-gray-600',
                    }
                }).then(() => {
                    // Reset form and switch to login
                    registerForm.reset();
                    signUpForm.classList.add("hidden");
                    signInForm.classList.remove("hidden");
                });
            } else {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: data.message,
                    customClass: {
                        popup: 'bg-white rounded-lg shadow-xl',
                        title: 'text-xl font-bold text-gray-900',
                        text: 'text-gray-600',
                    }
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred during registration.',
                customClass: {
                    popup: 'bg-white rounded-lg shadow-xl',
                    title: 'text-xl font-bold text-gray-900',
                    text: 'text-gray-600',
                }
            });
        });
    });

    // ✅ Handle Login
    document.getElementById("loginForm").addEventListener("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                Swal.fire({
                    title: "Success!",
                    text: data.message,
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    if (data.role === "admin") {
                        window.location.href = "admin_dashboard.php";
                    } else if (data.role === "user") {
                        window.location.href = "dashboard.php";
                    } else {
                        console.error("Unknown role:", data.role);
                        window.location.href = "index.php"; // Default fallback
                    }
                });
            } else {
                Swal.fire({ title: "Error!", text: data.message, icon: "error" })
                .then(() => clearLoginInputs());
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // ✅ Clear login fields on error
    function clearLoginInputs() {
        let usernameField = document.querySelector("#loginForm input[name='username']");
        let passwordField = document.querySelector("#loginForm input[name='password']");

        usernameField.value = "";
        passwordField.value = "";

        // Temporary set password to text then back to password for better UI reset
        passwordField.type = "text";
        setTimeout(() => passwordField.type = "password", 10);

        setTimeout(() => usernameField.focus(), 100);
    }

    // ✅ Handle Announcement Posting
    if (document.getElementById("announcementForm")) {
        document.getElementById("announcementForm").addEventListener("submit", function (e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch("post_announcement.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        title: "Success!",
                        text: data.message,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        document.getElementById("announcementForm").reset();
                    });
                } else {
                    Swal.fire({ title: "Error!", text: data.message, icon: "error" });
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }
});
