document.getElementById("registerBtn").addEventListener("click", function () {
    document.querySelector(".scene").classList.add("flip");
});

document.getElementById("loginBtn").addEventListener("click", function () {
    document.querySelector(".scene").classList.remove("flip");
});
