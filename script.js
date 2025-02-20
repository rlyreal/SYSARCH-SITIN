document.addEventListener("DOMContentLoaded", function () {
    const signUpButton = document.getElementById("signUpButton");
    const signInButton = document.getElementById("signInButton");
    const signInForm = document.getElementById("signIn");
    const signUpForm = document.getElementById("signup");

    signUpButton.addEventListener("click", function () {
        signInForm.style.display = "none";
        signUpForm.style.display = "block";
    });

    signInButton.addEventListener("click", function () {
        signInForm.style.display = "block";
        signUpForm.style.display = "none";
    });


    document.getElementById("registerForm").addEventListener("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("register.php", {
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
                    document.getElementById("signup").style.display = "none";
                    document.getElementById("signIn").style.display = "block";
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: data.message,
                    icon: "error"
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });

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
                    window.location.href = "dashboard.php";
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: data.message,
                    icon: "error"
                }).then(() => {
                    console.log("Clearing input fields...");

                    let usernameField = document.querySelector("#loginForm input[name='username']");
                    let passwordField = document.querySelector("#loginForm input[name='password']");

                    usernameField.value = "";
                    passwordField.value = "";

                    
                    usernameField.type = "text";
                    passwordField.type = "text";

                    setTimeout(() => {
                        usernameField.type = "text";
                        passwordField.type = "password";
                    }, 10);

                    setTimeout(() => {
                        usernameField.focus();
                    }, 100);
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
});
